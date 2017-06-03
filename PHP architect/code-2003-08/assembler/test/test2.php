<?php
dl("php_asm.dll");

asm("
	org 0x00000000
	use32
	
	mov	ecx, [esp+4]
	push	dword [ecx]
	pop	[load_lib]
	push	dword [ecx+4]
	pop	[get_prc]

	push	lib_u32
	call	[load_lib]
	push	mbname
	push	eax
	call	[get_prc]

	xor	ecx, ecx	

	push	ecx
	push	mb_title
	push	mb_text
	push	ecx
	call	eax
	ret
	
	load_lib dd 0
	get_prc dd 0
	lib_u32 db 'user32.dll',0
	mbname db 'MessageBoxA',0
	mb_title db 'hello world!',0
	mb_text  db 'The sky above the port was the color of television, tuned to a dead channel.',0
");
?>