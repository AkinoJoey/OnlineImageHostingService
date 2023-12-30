<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Helpers\DatabaseHelper;

class DailyAccessCheck extends AbstractCommand
{
    protected static ?string $alias = 'dac';

    public static function getArguments(): array
    {
        return [];
    }
    
    public function execute(): int
    {
        $this->log('Starting an access check.......');
        $this->accessCheck();
        return 0;
    }

    public function accessCheck() : void {
        date_default_timezone_set('Asia/Tokyo');
        
        $oldData = DatabaseHelper::getInactiveImageData30Days();

        $uploadDir = __DIR__ . '/../../public/uploads/';
        
        if(empty($oldData)){
            $this->log('No data exceeds 30 days.');
        }else{
            DatabaseHelper::deleteInactiveImageData30Days();

            foreach ($oldData as $file) {
                $currentData = $uploadDir . $file;
                unlink($currentData);
                $this->log("Delete "  . $currentData);
            }

            $this->log("Access check complete.");
        }

    }
}