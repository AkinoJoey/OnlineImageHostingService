<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Exception;

class CommandGeneration extends AbstractCommand
{
    // 使用するコマンド名を設定
    protected static ?string $alias = 'comm-gen';
    protected static bool $requiredCommandValue = true;

    // 引数を割り当て
    public static function getArguments(): array
    {
        return [];
    }

    public function execute(): int
    {
        $command = $this->getCommandValue();
        $this->log('Generating code for.......' . $command);
        $this->createCommandFile($command);
        $this->registry($command);
        return 0;
    }

    public function convertToCamelCase(string $command): string
    {
        $words = explode("-", $command);
        $camelCaseWorlds = array_map('ucfirst', $words);
        return implode('', $camelCaseWorlds);
    }

    public function createCommandFile(string $command): void
    {
        $className = $this->convertToCamelCase($command);

        $boilerPlateCode = sprintf(<<<'EOD'
        <?php

        namespace Commands\Programs;

        use Commands\AbstractCommand;
        use Commands\Argument;

        class %s extends AbstractCommand
        {
            // TODO: エイリアスを設定してください。
            protected static ?string $alias = '%s';

            // TODO: 引数を設定してください。
            public static function getArguments(): array
            {
                return [];
            }

            // TODO: 実行コードを記述してください。
            public function execute(): int
            {
                return 0;
            }
        }
        EOD, $className, $command);

        $filename = 'Commands/Programs/' . $className . '.php';

        if (file_exists($filename)) throw new Exception("ファイルが既に存在します。");

        file_put_contents($filename, $boilerPlateCode);
    }

    public function registry($command): void
    {
        $registryDir = 'Commands/registry.php';
        $commands = file_get_contents($registryDir);

        $className = $this->convertToCamelCase($command);
        $newCommands = sprintf('    ' . 'Commands\Programs\%s::class' . ',' . PHP_EOL, $className);
        $add_index = strrpos($commands, '];');

        $commands = substr_replace($commands, $newCommands, $add_index, 0);
        file_put_contents($registryDir, $commands);
    }
}
