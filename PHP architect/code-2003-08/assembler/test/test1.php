<?php
dl("php_asm.dll");

$return = asm("
	org 0x00000000
	use32
	
	mov edx, dword [sux]
	mov ebx, 0x95511559
	xor edx, ebx
	push edx
	pop eax
	ret
	sux dd 0x87654321
");
printf("%s %x","asmed:", $return);
?>
