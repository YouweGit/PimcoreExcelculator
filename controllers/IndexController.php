<?php


class PimcoreExcelculator_IndexController extends \Pimcore\Controller\Action\Admin
{

    public function stopAction() {
        $timestamp_string = date('Y-m-d H:i:s');
        $data = 'Attempting to stop service';

        try {
            $calcey = new \PimcoreExcelculator\PimcoreExcelculatorCalc();
            $calcey->stop();
        }
        catch (\Throwable $t)
        {
            $data = 'Error ' . $t->getCode() . ': ' . $t->getMessage();
        }

        $this->_helper->json(array(
            'success'   => true,
            'message'   => "Attempted to stop service",
            'timestamp' => $timestamp_string,
            'data'      => $data
        ));
    }

    public function statusAction() {
        $timestamp_string = date('Y-m-d H:i:s');
        $data = [];
        $data['statusString'] = 'status ok';
        $data['status'] = true;

        try {
            $calcey = new \PimcoreExcelculator\PimcoreExcelculatorCalc();
            $data = $calcey->status();
        }
        catch (\Throwable $t)
        {
            $data['status'] = false;
            $data['statusString'] = 'Error ' . $t->getCode() . ': ' . $t->getMessage();
        }

        $this->_helper->json(array(
            'success'   => true,
            'message'   => "Check completed",
            'timestamp' => $timestamp_string,
            'data'      => $data
        ));
    }

    public function testAction() {
        $timestamp_string = date('Y-m-d H:i:s');
        $result = 'false';

        try
        {
            $calcey = new \PimcoreExcelculator\PimcoreExcelculatorCalc('included-demo-file');
            $calcey->set([
                'A3' => 420,
                'A4' => 246
            ]);

            $results = $calcey->get(['A5']);

            $calcey = new \PimcoreExcelculator\PimcoreExcelculatorCalc('included-demo-file');
            $calcey->set([
                'A3' => 421,
                'A4' => 246
            ]);

            $results2 = $calcey->get(['A5']);

            if($results['A5'] == 666 && $results2['A5'] == 667) {
                $result = true;
            } else {

            }
            $data = 'Performed two calculations on included demo excel sheet';
        }
        catch (\Throwable $t)
        {
            $data = 'Error: ' . $t->getMessage();
        }

        $data .= '<br/>Success: ' . var_export($result,true);

        $this->_helper->json(array(
            'success'   => true,
            'message'   => "Test completed",
            'timestamp' => $timestamp_string,
            'data'      => $data,
            'result'    => $result
        ));
    }

    public function logAction() {

        $logfile = PIMCORE_LOG_DIRECTORY . '/PimcoreExcelculator.log';
        $timestamp_string = date('Y-m-d H:i:s');
        $logdata = $this->getTail($logfile, 5000);
        $logdata = explode("\n", $logdata);
        $logdata = array_reverse($logdata);
        $logdata = implode("\n", $logdata);
        $logdata = nl2br($logdata);

        $this->_helper->json(array(
            'success'   => true,
            'message'   => "Log tail retrieved",
            'timestamp' => $timestamp_string,
            'data'      => $logdata
        ));
    }

    private function getTail($filepath, $lines = 1, $adaptive = true) {
        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) return false;
        // Sets buffer size, according to the number of lines to retrieve.
        // This gives a performance boost when reading a few lines from the file.
        if (!$adaptive) $buffer = 4096;
        else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
        // Jump to last character
        fseek($f, -1, SEEK_END);
        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") $lines -= 1;

        // Start reading
        $output = '';
        $chunk = '';
        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {
            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);
            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);
            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;
            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }
        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }
        // Close file and return
        fclose($f);
        return trim($output);
    }

}
