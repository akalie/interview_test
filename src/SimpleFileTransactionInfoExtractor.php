<?php declare(strict_types=1);


namespace blabla;


use InvalidArgumentException;
use SplFileObject;

/**
    processes file with transactions info in given format (like clickhouse JSONEachRow):
{"bin":"41417360","amount":"130.00","currency":"USD"}
{"bin":"12323123","amount":"210.00","currency":"USD"}
...
 */
class SimpleFileTransactionInfoExtractor
{
    protected SplFileObject $file;

    public function __construct(string $filepath)
    {
        if (!file_exists($filepath)) {
            throw new InvalidArgumentException('Failed to find file ' . $filepath);
        }

        $this->file = new SplFileObject($filepath);
    }

    public function next(): ?TransactionInfo
    {
        $this->file->next();
        $transactionJson = $this->file->current();
        if (empty($transactionJson)) {
            return null;
        }
        $tmp = json_decode($transactionJson, true, 512, JSON_THROW_ON_ERROR);

        if (!isset($tmp['bin'], $tmp['amount'], $tmp['currency'])) {
            throw new InvalidArgumentException('Failed to unpack transaction info ' . $transactionJson);
        }

        return new TransactionInfo(
            (int) $tmp['bin'],
            (float) $tmp['amount'],
            $tmp['currency']
        );
    }
}