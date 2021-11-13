<?php

class MixpanelGroupsProducerTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Producers_MixpanelGroups
     */
    protected $_instance = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_instance = new Producers_MixpanelGroups("token");
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->_instance->reset();
        $this->_instance = null;
    }

    public function testSet() {
        $this->_instance->set("company","Mixpanel", array("industry" => "tech"));
        $queue = $this->_instance->getQueue();
        $msg = $queue[count($queue)-1];

        $this->assertEquals("company", $msg['$group_key']);
        $this->assertEquals("Mixpanel", $msg['$group_id']);
        $this->assertEquals("token", $msg['$token']);
        $this->assertArrayNotHasKey('$ignore_time', $msg);
        $this->assertArrayHasKey('$set', $msg);
        $this->assertArrayHasKey("industry", $msg['$set']);
        $this->assertEquals("tech", $msg['$set']['industry']);
    }

    public function testSetIgnoreTime() {
        $this->_instance->set("company","Mixpanel", array("industry" => "Tech"), true);
        $queue = $this->_instance->getQueue();
        $msg = $queue[count($queue)-1];

        $this->assertEquals("company", $msg['$group_key']);
        $this->assertEquals("Mixpanel", $msg['$group_id']);
        $this->assertEquals("token", $msg['$token']);
        $this->assertEquals(true, $msg['$ignore_time']);
        $this->assertArrayHasKey('$set', $msg);
        $this->assertArrayHasKey("industry", $msg['$set']);
        $this->assertEquals("tech", $msg['$set']['industry']);
    }


    public function testSetOnce() {
        $this->_instance->setOnce("company","Mixpanel", array("industry" => "Tech"), true);
        $queue = $this->_instance->getQueue();
        $msg = $queue[count($queue)-1];

        $this->assertEquals("company", $msg['$group_key']);
        $this->assertEquals("Mixpanel", $msg['$group_id']);
        $this->assertEquals("token", $msg['$token']);
        $this->assertArrayNotHasKey('$ignore_time', $msg);
        $this->assertArrayHasKey('$set', $msg);
        $this->assertArrayHasKey("industry", $msg['$set_once']);
        $this->assertEquals("tech", $msg['$set']['industry']);
    }

    public function testAppendSingle() {
        $this->_instance->append("company","Mixpanel", "actions", "Logged In");
        $queue = $this->_instance->getQueue();
        $msg = $queue[count($queue)-1];

        $this->assertEquals("company", $msg['$group_key']);
        $this->assertEquals("Mixpanel", $msg['$group_id']);
        $this->assertArrayHasKey('$append', $msg);
        $this->assertArrayHasKey("actions", $msg['$append']);
        $this->assertEquals("Logged In", $msg['$append']['actions']);
    }

    public function testAppendMultiple() {
        $this->_instance->append("company","Mixpanel", "actions", array("Logged In", "Logged Out"));
        $queue = $this->_instance->getQueue();
        $msg = $queue[count($queue)-1];

        $this->assertEquals("company", $msg['$group_key']);
        $this->assertEquals("Mixpanel", $msg['$group_id']);
        $this->assertEquals("token", $msg['$token']);
        $this->assertArrayHasKey('$union', $msg);
        $this->assertArrayHasKey("actions", $msg['$union']);
        $this->assertEquals(array("Logged In", "Logged Out"), $msg['$union']['actions']);
    }


    public function testRemove() {
        $this->_instance->remove("company","Mixpanel", array("industry" => "tech"));
        $queue = $this->_instance->getQueue();
        $msg = $queue[count($queue)-1];

        $this->assertEquals("company", $msg['$group_key']);
        $this->assertEquals("Mixpanel", $msg['$group_id']);
        $this->assertEquals("token", $msg['$token']);
        $this->assertArrayNotHasKey('$ignore_time', $msg);
        $this->assertArrayHasKey('$unset', $msg);
        $this->assertArrayHasKey("industry", $msg['$set']);
        $this->assertEquals("tech", $msg['$set']['industry']);
    }


    public function testDeleteGroup() {
        $this->_instance->deleteGroup("company","Mixpanel");
        $queue = $this->_instance->getQueue();
        $msg = $queue[count($queue)-1];

        $this->assertEquals("company", $msg['$group_key']);
        $this->assertEquals("Mixpanel", $msg['$group_id']);
        $this->assertEquals("token", $msg['$token']);
        $this->assertArrayHasKey('$delete', $msg);
        $this->assertEquals("", $msg['$delete']);
    }
}
