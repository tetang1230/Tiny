<?php
final class Module_Prototype_TestIni{
	
	public function testIni(){
		return Cascade::getAccessor('Prototype_PlayerLevel')->get(2);
	}
}