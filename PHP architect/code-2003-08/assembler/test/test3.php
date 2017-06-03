<?php
dl("php_asm.dll");
$m_text = "It was a Russian military prosthesis, a seven-function force-feedback manipulator, cased in grubby pink plastic.";
$m_type = 0; //MB_OK
asm("
	org 0x00000000
	use32

	mov	ecx, [esp+4]
	push	dword [ecx]
	pop	[load_lib]
	push	dword [ecx+4]
	pop	[get_prc]
	push	dword [ecx+0xC]
	pop	[get_var_data]

	push	lib_u32
	call	[load_lib]
	push	mbname
	push	eax
	call	[get_prc]
	mov	[MessageBoxA], eax

	push	mb_flags
	call	[get_var_data]
	push	eax
	
	push	mb_title

	push	mb_text
	call	[get_var_data]
	push	eax	
	push	dword 0
	call	[MessageBoxA]
	ret
	
	load_lib dd 0
	get_prc dd 0
	get_var_data dd 0
	MessageBoxA dd 0
	lib_u32 db 'user32.dll',0
	mbname db 'MessageBoxA',0
	mb_title db 'hello world!',0
	mb_text  db 'm_text',0
	mb_flags db 'm_type',0
");
?>