<?php

require_once 'phing/tasks/ext/dbdeploy/DbDeployTask.php';

class BatchDeployTask extends DbDeployTask
{
   function init()
   {
      $this->setDeltaSet('Batch');
   }

   function doDeploy()
   {
      $lastChangeAppliedInDb = $this->getLastChangeAppliedInDb();

      $files = $this->getDeltasFilesArray();
      ksort($files);

      foreach($files as $fileChangeNumber=>$fileName)
      {
         if($fileChangeNumber > $lastChangeAppliedInDb && $fileChangeNumber <= $this->lastChangeToApply)
         {
            $dbh = new PDO($this->url, $this->userid, $this->password);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sqlToStartDeploy = 'INSERT INTO ' . DbDeployTask::$TABLE_NAME . ' (change_number, delta_set, start_dt, applied_by, description)'.
               ' VALUES ('. $fileChangeNumber .', \''. $this->deltaSet .'\', '. $this->dbmsSyntax->generateTimestamp() .', \'batchdeploy\', \''. $fileName .'\');' . "\n";
            $dbh->exec($sqlToStartDeploy);

            $fullFileName = $this->dir . '/' . $fileName;
            $this->log("Running $fullFileName");
            require $fullFileName;

            $sqlToEndDeploy = 'UPDATE ' . DbDeployTask::$TABLE_NAME . ' SET complete_dt = ' . $this->dbmsSyntax->generateTimestamp() . ' WHERE change_number = ' . $fileChangeNumber . ' AND delta_set = \'' . $this->deltaSet . '\';' . "\n";
            $dbh->exec($sqlToEndDeploy);
         }
      }
   }
}


