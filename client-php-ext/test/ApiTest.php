<?php

define("HGL_SUCCESS", 0);

define("HISTORY_GLUON_DELETE_TYPE_EQUAL",            0);
define("HISTORY_GLUON_DELETE_TYPE_EQUAL_OR_LESS",    1);
define("HISTORY_GLUON_DELETE_TYPE_LESS",             2);
define("HISTORY_GLUON_DELETE_TYPE_EQUAL_OR_GREATER", 3);
define("HISTORY_GLUON_DELETE_TYPE_GREATER",          4);

define("HISTORY_GLUON_DATA_KEY_ID",   "id");
define("HISTORY_GLUON_DATA_KEY_SEC",  "sec");
define("HISTORY_GLUON_DATA_KEY_NS",   "ns");
define("HISTORY_GLUON_DATA_KEY_TYPE", "type");
define("HISTORY_GLUON_DATA_KEY_DATA", "data");

define("HISTORY_GLUON_DATA_TYPE_FLOAT",  0);
define("HISTORY_GLUON_DATA_TYPE_STRING", 1);
define("HISTORY_GLUON_DATA_TYPE_UINT",   2);
define("HISTORY_GLUON_DATA_TYPE_BLOB",   3);

define("HISTORY_GLUON_SORT_ASCENDING",  0);
define("HISTORY_GLUON_SORT_DESCENDING", 1);
define("HISTORY_GLUON_SORT_NOT_SORTED", 2);

define("HISTORY_GLUON_TIME_START_SEC",  0x00000000);
define("HISTORY_GLUON_TIME_START_NS",   0x00000000);
define("HISTORY_GLUON_TIME_END_SEC",    0x99999999);
define("HISTORY_GLUON_TIME_END_NS",     0x99999999);

define("HISTORY_GLUON_NUM_ENTRIES_UNLIMITED",  0);

define("TEST_ID_UINT",   0x358);
define("TEST_ID_FLOAT",  0x12345678);
define("TEST_ID_STRING", 0x87654321);
define("TEST_ID_BLOB",   0x102030405060);

define("TEST_NUM_SAMPLES", 5);

class ApiTest extends PHPUnit_Framework_TestCase
{
    /* ------------------------------------------------------------------------
     * Private Members
     * --------------------------------------------------------------------- */
    private $g_ctx = null;

    /* ------------------------------------------------------------------------
     * Test Methods
     * --------------------------------------------------------------------- */
    protected function setUp() {
        if (!extension_loaded('History Gluon PHP Extension'))
            dl("history_gluon.so");
    }

    protected function tearDown() {
        if ($this->g_ctx != null) {
            history_gluon_free_context($this->g_ctx);
            $this->g_ctx = null;
        }
    }

    public function testCreateContext() {
        $ctx = history_gluon_create_context();
        $this->assertGreaterThan(0, $ctx);
    }

    public function testFreeContext() {
        $this->assertGloblCreateContext();
        history_gluon_free_context($this->g_ctx);
        $this->g_ctx = null;
    }

    public function testAddUint() {
        $id   = 0x12345678;
        $sec  = 10203040;
        $ns   = 200111500;
        $data = 0x87654321;
        $this->assertGloblCreateContext();
        $this->assertDeleteAllDataWithId($id);
        $ret = history_gluon_add_uint($this->g_ctx, $id, $sec, $ns, $data);
        $this->assertEquals(HGL_SUCCESS, $ret);
    }

    public function testRangeQueryUint() {
        $idx0 = 2;
        $num = 2;
        $ts1plug1ns = False;
        $sortRequest = HISTORY_GLUON_SORT_ASCENDING;
        $expectedRetIdx0 = $idx0;
        $expectedNumRet = $num;
        $this->assertRangeQuery(TEST_ID_UINT, $idx0, $num, $ts1plug1ns,
                                $sortRequest,
                                $expectedRetIdx0, $expectedNumRet);
    }

    /* -----------------------------------------------------------------------
     * Private Methods
     * --------------------------------------------------------------------- */
    private function assertGloblCreateContext() {
        $this->g_ctx = history_gluon_create_context();
        $this->assertGreaterThan(0, $this->g_ctx);
    }

    private function assertDeleteAllDataWithId($id) {
        $num_deleted = 0;
        $ret = history_gluon_delete($this->g_ctx, $id, 0, 0,
                                    HISTORY_GLUON_DELETE_TYPE_EQUAL_OR_GREATER,
                                    $num_deleted);
        $this->assertEquals(HGL_SUCCESS, $ret);
    }

    private function assertCreateContextDeleteAddSamples($id) {
        $this->assertGloblCreateContext();
        $this->assertDeleteAllDataWithId($id);
        $addFuncName = $this->getSamplesAddFuncName($id);
        $this->assertAddSamples($addFuncName);
    }

    private function getSamplesAddFuncName($id) {
        $name = null;
        if ($id == TEST_ID_UINT)
            $name = "getSamplesUint";
        else if ($id == TEST_ID_FLOAT)
            $name = "getSamplesFloat";
        else if ($id == TEST_ID_STRING)
            $name = "getSamplesString";
        else if ($id == TEST_ID_BLOB)
            $name = "getSamplesBlob";
        else
            $this->ssertFalse(TRUE);
        return $name;
    }

    private function getSamples($id) {
        $addFuncName = $this->getSamplesAddFuncName($id);
        return call_user_func(array($this, $addFuncName));
    }

    private function assertAddSamples($getSamplesFunc) {
        $samples = call_user_func(array($this, $getSamplesFunc));
        $num_samples = count($samples);
        for ($i = 0; $i < $num_samples; $i++) {
            $id   = $samples[$i][HISTORY_GLUON_DATA_KEY_ID];
            $sec  = $samples[$i][HISTORY_GLUON_DATA_KEY_SEC];
            $ns   = $samples[$i][HISTORY_GLUON_DATA_KEY_NS];
            $data = $samples[$i][HISTORY_GLUON_DATA_KEY_DATA];
            $ret = history_gluon_add_uint($this->g_ctx, $id, $sec, $ns, $data);
            $this->assertEquals(HGL_SUCCESS, $ret);
        }
    }

    private function getSamplesUint() {
        $data_type = HISTORY_GLUON_DATA_TYPE_UINT;
        $samples[0] = $this->createSample(0x12345678, 100200300,
                                          $data_type, 0x43214321);
        $samples[1] = $this->createSample(0x20000000, 000000000,
                                          $data_type, 0x22223333);
        $samples[2] = $this->createSample(0x20000000, 000000003,
                                          $data_type, 0x9a9a8b8b);
        $samples[3] = $this->createSample(0x25010101, 200345003,
                                          $data_type, 0x9a9a8b8b00);
        $samples[4] = $this->createSample(0x353a0000, 879200003,
                                          $data_type, 0x876543210fedcba);
        return $samples;
    }

    private function createSample($sec, $ns, $data_type, $data) {
        $sample[HISTORY_GLUON_DATA_KEY_ID]   = TEST_ID_UINT;
        $sample[HISTORY_GLUON_DATA_KEY_SEC]  = $sec;
        $sample[HISTORY_GLUON_DATA_KEY_NS]   = $ns;
        $sample[HISTORY_GLUON_DATA_KEY_TYPE] = $data_type;
        $sample[HISTORY_GLUON_DATA_KEY_DATA] = $data;
        return $sample;
    }

    private function getSampleTime($id, $idx) {
        $sample = $this->getSamples($id)[$idx];
        $sec = $sample[HISTORY_GLUON_DATA_KEY_SEC];
        $ns  = $sample[HISTORY_GLUON_DATA_KEY_NS];
        return array($sec, $ns);
    }

    private function addTime($time0, $time1) {
        $sec0 = $time0[0];
        $ns0  = $time0[1];
        $sec1 = $time1[0];
        $ns1  = $time1[1];
        $sumSec = $sec0 + $sec1;
        $sumNs = $sec0 + $sec1;

        $sumSec += $sumNs / 1000000000;
        $sumNs = $sumNs % 1000000000;
        return array($sumSec, $sumNs);
    }

    private function assertRangeQuery($id, $idx0, $num, $ts1plug1ns,
                                      $sortRequest,
                                      $expectedRetIdx0, $expectedNumRet) {
        $this->assertCreateContextDeleteAddSamples($id);
        $numMaxEntries = HISTORY_GLUON_NUM_ENTRIES_UNLIMITED;
        $array = null;
        $idx1 = $idx0 + $num;
        $time0 = $this->getSampleTime($id, $idx0);
        $time1 = $this->getSampleTime($id, $idx1);
        if ($ts1plug1ns) {
            $timeInc = array(0, 1);
            $time1 = $this->addTIme($time1, $timeInc);
        }
        $sec0 = $time0[0];
        $ns0  = $time0[1];
        $sec1 = $time1[0];
        $ns1  = $time1[1];
        $ret = history_gluon_range_query($this->g_ctx, $id,
                                         $sec0, $ns0, $sec1, $ns1,
                                         $sortRequest, $numMaxEntries,
                                         $array);
        $this->assertEquals(HGL_SUCCESS, $ret);
    }

}