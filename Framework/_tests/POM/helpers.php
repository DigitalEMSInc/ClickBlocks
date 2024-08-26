<?php

namespace ClickBlocks\POMTest;

use ClickBlocks\Core,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

/**
 * @group Helpers 
 */ 
class HelpersTest extends \PHPUnit_Framework_TestCase
{
   protected $stub = null;
   
   //public static function setUpBeforeClass(){}
   
   protected function setUp()
   {
      $this->stub = $this->getMockForAbstractClass('\ClickBlocks\Web\UI\Helpers\Control', array('ctrl'));
      $this->stub->expects($this->any())->method('render')->will($this->returnValue(''));
   }
   
   //protected function assertPreConditions(){}
   //protected function assertPostConditions(){}
   //protected function tearDown(){}
   //public static function tearDownAfterClass(){}
   //protected function onNotSuccessfulTest(Exception $e){}
   
   public function testControl_getParameters()
   {
      $temp = array(array('id' => 'ctrl', 'style' => null, 'class' => null, 'title' => null, 'lang' => null, 'dir' => null, 'onfocus' => null, 'onblur' => null, 'onclick' => null, 'ondblclick' => null, 'onmousedown' => null, 'onmouseup' => null, 'onmouseover' => null, 'onmousemove' => null, 'onmouseout' => null, 'onkeypress' => null, 'onkeydown' => null, 'onkeyup' => null), array('showID' => false));
      $this->assertEquals($temp, $this->stub->getParameters());
   }
   
   /**
    * @depends testControl_getParameters
    * @covers \ClickBlocks\Web\UI\Helpers\Control::getParameters 
    */
   public function testControl_setParameters()
   {
      $temp = array(array('id' => 'newctrl', 'style' => null, 'class' => 'cssclass', 'title' => null, 'lang' => 'en', 'dir' => null, 'onfocus' => null, 'onblur' => null, 'onclick' => 'alert(0);', 'ondblclick' => null, 'onmousedown' => null, 'onmouseup' => null, 'onmouseover' => null, 'onmousemove' => null, 'onmouseout' => null, 'onkeypress' => null, 'onkeydown' => null, 'onkeyup' => null), array('showID' => true));
      $this->stub->setParameters($temp);
      $this->assertEquals($temp, $this->stub->getParameters());
   }
   
   /**
    * @covers \ClickBlocks\Web\UI\Helpers\Control::__get
    */
   public function testControl_get()
   {
      $this->assertEquals($this->stub->id, 'ctrl');
   }
   
   /**
    * @depends testControl_get
    * @expectedException \Exception
    * @covers \ClickBlocks\Web\UI\Helpers\Control::__get
    */
   public function testControl_get_Exception()
   {
      $x = $this->stub->foo;
   } 
   
   /**
    * @depends testControl_get
    * @covers \ClickBlocks\Web\UI\Helpers\Control::__set
    */
   public function testControl_set()
   {
      $this->stub->id = 'test';
      $this->assertEquals($this->stub->id, 'test');
      $this->stub->class = 'foo';
      $this->assertEquals($this->stub->class, 'foo');
   }
   
   /**
    * @depends testControl_set
    * @expectedException \Exception
    */ 
   public function testControl_set_Exception()
   {
      $this->stub->foo = true;
   }
   
   public function testControl_isset()
   {
      $this->assertTrue(isset($this->stub->style));
      $this->assertFalse(isset($this->stub->foo));  
   }
   
   /**
    * @depends testControl_isset
    */
   public function testControl_unset()
   {
      unset($this->stub->id);
      $this->assertFalse(isset($this->stub->id));
   }
   
   /**
    * @depends testControl_get
    */
   public function testControl_addClass()
   {
      $this->stub->addClass('class1');
      $this->stub->addClass('class2');
      $this->assertEquals($this->stub->class, 'class1 class2');
   }
   
   /**
    * @depends testControl_addClass
    * @covers \ClickBlocks\Web\UI\Helpers\Control::hasClass
    */
   public function testControl_hasClass()
   {
      $this->assertFalse($this->stub->hasClass('foo'));
      $this->stub->addClass('class1')->addClass('foo')->addClass('class2');
      $this->assertTrue($this->stub->hasClass('foo'));
   }
   
   /**
    * @depends testControl_hasClass
    * @depends testControl_addClass
    * @covers \ClickBlocks\Web\UI\Helpers\Control::removeClass
    */
   public function testControl_removeClass()
   {
      $this->stub->addClass('class1')->addClass('foo')->addClass('class2');
      $this->stub->removeClass('foo');
      $this->assertFalse($this->stub->hasClass('foo'));
      $this->assertTrue($this->stub->hasClass('class1'));
      $this->assertTrue($this->stub->hasClass('class2'));
   }
   
   /**
    * @depends testControl_removeClass
    * @depends testControl_addClass
    * @covers \ClickBlocks\Web\UI\Helpers\Control::replaceClass
    */
   public function testControl_replaceClass()
   {
      $this->stub->addClass('class1')->addClass('class2');
      $this->stub->replaceClass('class1', 'foo');
      $this->assertTrue($this->stub->hasClass('foo'));
   }
   
   /**
    * @depends testControl_replaceClass
    * @depends testControl_hasClass
    * @covers \ClickBlocks\Web\UI\Helpers\Control::togglelass
    */
   public function testControl_toggleClass()
   {
      $this->stub->addClass('class1')->addClass('class2')->addClass('class3');
      $this->stub->toggleClass('class1', 'foo');
      $this->assertTrue($this->stub->hasClass('foo'));
      $this->stub->toggleClass('foo', 'class1');
      $this->assertTrue($this->stub->hasClass('class1'));
   }
   
   public function testStaticText_render()
   {
      $ctrl = new Helpers\StaticText('stxt', '<b>Hello World</b>');
      $this->assertEquals($ctrl->render(), '<span><b>Hello World</b></span>');
      $ctrl->showID = true;
      $this->assertEquals($ctrl->render(), '<span id="stxt"><b>Hello World</b></span>');
      $ctrl->text = '<i>Hello World!!!</i>';
      $ctrl->onclick = 'alert(0);';
      $ctrl->tag = 'div';
      $this->assertEquals($ctrl->render(), '<div id="stxt" onclick="alert(0);"><i>Hello World!!!</i></div>');
   }
   
   public function testLink_render()
   {
      $ctrl = new Helpers\Link('lnk', 'http://google.com', '<b>Goggle!</b>');
      $ctrl->class = 'red_link';
      $ctrl->target = '_blank';
      $this->assertEquals($ctrl->render(), '<a class="red_link" href="http://google.com" target="_blank"><b>Goggle!</b></a>');
      $ctrl->showID = true;
      $ctrl->onmousemove = 'move(this.id)';
      $this->assertEquals($ctrl->render(), '<a id="lnk" class="red_link" onmousemove="move(this.id)" href="http://google.com" target="_blank"><b>Goggle!</b></a>');
   }
   
   public function testImg_render()
   {
      $ctrl = new Helpers\Img('img', '/i/backend/logo.png', 'logo');
      $this->assertEquals($ctrl->render(), '<img src="/i/backend/logo.png" alt="logo" />');
      $ctrl->showID = true;
      $ctrl->width = 100;
      $ctrl->height = 50;
      $ctrl->ondblclick = 'return false;';
      $this->assertEquals($ctrl->render(), '<img id="img" ondblclick="return false;" src="/i/backend/logo.png" alt="logo" width="100" height="50" />');
   }
   
   public function testIFrame_render()
   {
      $ctrl = new Helpers\IFrame('frm');
      $ctrl->name = 'mainform';
      $ctrl->src = 'http://domain/test.html';
      $this->assertEquals($ctrl->render(), '<iframe name="mainform" src="http://domain/test.html"></iframe>');
      $ctrl->showID = true;
      $ctrl->onload = 'frm.submit();';
      $ctrl->frameborder = 0;
      $this->assertEquals($ctrl->render(), '<iframe id="frm" name="mainform" src="http://domain/test.html" onload="frm.submit();"></iframe>');
   }
   
   public function testScript_render()
   {
      $ctrl = new Helpers\Script();
      $ctrl->text = 'ajax = new Ajax();';
      $this->assertEquals($ctrl->render(), '<script type="text/javascript">ajax = new Ajax();</script>');
      $ctrl->text = '';
      $ctrl->src = '/js/mootools.js';
      $this->assertEquals($ctrl->render(), '<script type="text/javascript" src="/js/mootools.js"></script>');
   }
   
   public function testStyle_render()
   {
      $ctrl = new Helpers\Style(null, '.red_link {color: red;}');
      $this->assertEquals($ctrl->render(), '<style type="text/css">.red_link {color: red;}</style>');
      $ctrl->href = '/css/styles.css';
      $ctrl->media = 'all';
      $this->assertEquals($ctrl->render(), '<link type="text/css" href="/css/styles.css" rel="stylesheet" media="all" />');
   }
   
   public function testMeta_render()
   {
      $ctrl = new Helpers\Meta();
      $ctrl->{'http-equiv'} = 'X-UA-Compatible';
      $ctrl->content = 'IE=edge,chrome=1';
      $this->assertEquals($ctrl->render(), '<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible" />');
      $ctrl->{'http-equiv'} = '';
      $ctrl->name = 'viewport';
      $ctrl->content = 'width=device-width; initial-scale=1.0; maximum-scale=1.0;';
      $this->assertEquals($ctrl->render(), '<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />');
   }
   
   /**
    * @depends testStyle_render
    * @depends testMeta_render
    * @covers \ClickBlocks\Web\UI\Helpers\Head::render 
    */
   public function testHead_render()
   {
      $meta = new Helpers\Meta();
      $meta->{'http-equiv'} = 'X-UA-Compatible';
      $meta->content = 'IE=edge,chrome=1';
      $ctrl = new Helpers\Head('Test Page');
      $ctrl->icon = 'favicon.ico';
      $ctrl->addMeta($meta);
      $this->assertEquals($ctrl->render(), '<head><meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible" /><title>Test Page</title><link type="image/x-icon" href="favicon.ico" rel="shortcut icon" /></head>');
      $ctrl->icon = '';
      $ctrl->deleteMeta($meta->id);
      $this->assertEquals($ctrl->render(), '<head><title>Test Page</title></head>');
   }
}

?>
