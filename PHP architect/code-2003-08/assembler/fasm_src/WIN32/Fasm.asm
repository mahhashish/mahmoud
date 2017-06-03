
; flat assembler source
; Copyright (c) 1999-2002, Tomasz Grysztar
; All rights reserved.

	format MS COFF	

;extrn ExitProcess:dword for delphi
extrn __imp__ExitProcess@4:dword
ExitProcess equ __imp__ExitProcess@4

extrn __imp__CreateFileA@28:dword
CreateFile equ __imp__CreateFileA@28

extrn __imp__ReadFile@20:dword
ReadFile equ __imp__ReadFile@20

extrn __imp__WriteFile@20:dword
WriteFile equ __imp__WriteFile@20

extrn __imp__CloseHandle@4:dword
CloseHandle equ __imp__CloseHandle@4

extrn __imp__SetFilePointer@16:dword
SetFilePointer equ __imp__SetFilePointer@16

extrn __imp__GetCommandLineA@0:dword
GetCommandLine equ __imp__GetCommandLineA@0

extrn __imp__GetStdHandle@4:dword
GetStdHandle equ __imp__GetStdHandle@4

extrn __imp__VirtualAlloc@16:dword
VirtualAlloc equ __imp__VirtualAlloc@16

extrn __imp__GetTickCount@0:dword
GetTickCount equ __imp__GetTickCount@0

extrn __imp__GlobalMemoryStatus@4:dword
GlobalMemoryStatus equ __imp__GlobalMemoryStatus@4

public _compile_asm

_compile_asm:
	mov	esi,_logo
	call	display_string

	;call	get_params
	;cmp	[params],0
	;je	information
	;lea	eax,[params+1]
	;;mov	[input_file],eax
	mov [str_src], eax
	mov [str_src_len], edx
	;movzx	ecx,byte [eax-1]
	;add	eax,ecx
	;cmp	byte [eax],0
	;je	information
	;inc	eax
	mov	[output_file],edx

	call	init_memory

	mov	edi,characters
	mov	ecx,100h
	xor	al,al
      make_characters_table:
	stosb
	inc	al
	loop	make_characters_table
	mov	esi,characters+'a'
	mov	edi,characters+'A'
	mov	ecx,26
	rep	movsb
	mov	edi,characters
	mov	esi,symbol_characters+1
	movzx	ecx,byte [esi-1]
	xor	ebx,ebx
      convert_table:
	lodsb
	mov	bl,al
	mov	byte [edi+ebx],0
	loop	convert_table

	call	[GetTickCount]
	mov	[start_time],eax

	call	preprocessor
	call	parser
	call	assembler
	call	formatter
	ret
	movzx	eax,[current_pass]
	inc	al
	call	display_number
	mov	esi,_passes_suffix
	call	display_string
	call	[GetTickCount]
	sub	eax,[start_time]
	xor	edx,edx
	mov	ebx,100
	div	ebx
	or	eax,eax
	jz	display_bytes_count
	xor	edx,edx
	mov	ebx,10
	div	ebx
	push	edx
	call	display_number
	mov	dl,'.'
	call	display_character
	pop	eax
	call	display_number
	mov	esi,_seconds_suffix
	call	display_string
      display_bytes_count:
	mov	eax,[written_size]
	call	display_number
	mov	esi,_bytes_suffix
	call	display_string
	xor	al,al
	jmp	exit_program

information:
	mov	esi,_usage
	call	display_string
	mov	al,1
	jmp	exit_program

get_params:
	call	[GetCommandLine]
	mov	esi,eax
	mov	edi,params
    find_command_start:
	lodsb
	cmp	al,20h
	je	find_command_start
	cmp	al,22h
	je	skip_quoted_name
    skip_name:
	lodsb
	cmp	al,20h
	je	find_param
	or	al,al
	jz	all_params
	jmp	skip_name
    skip_quoted_name:
	lodsb
	cmp	al,22h
	je	find_param
	or	al,al
	jz	all_params
	jmp	skip_quoted_name
    find_param:
	lodsb
	cmp	al,20h
	je	find_param
	cmp	al,22h
	je	string_param
	cmp	al,0Dh
	je	all_params
	or	al,al
	jz	all_params
	inc	edi
	mov	ebx,edi
    copy_param:
	stosb
	lodsb
	cmp	al,20h
	je	param_end
	or	al,al
	jz	param_end
	jmp	copy_param
    string_param:
	inc	edi
	mov	ebx,edi
    copy_string_param:
	lodsb
	cmp	al,22h
	je	string_param_end
	or	al,al
	jz	param_end
	stosb
	jmp	copy_string_param
    param_end:
	dec	esi
    string_param_end:
	xor	al,al
	stosb
	mov	eax,edi
	sub	eax,ebx
	mov	[ebx-1],al
	jmp	find_param
    all_params:
	xor	al,al
	stosb
	ret

include 'system.inc'

include '..\version.inc'
include '..\errors.inc'
include '..\expressi.inc'
include '..\preproce.inc'
include '..\parser.inc'
include '..\assemble.inc'
include '..\formats.inc'
include '..\tables.inc'

_copyright db 'Copyright (c) 1999-2002, Tomasz Grysztar',0

_logo db 'flat assembler  version ',VERSION_STRING,0Dh,0Ah,0
_usage db 'usage: fasm source output',0Dh,0Ah,0

_passes_suffix db ' passes, ',0
_seconds_suffix db ' seconds, ',0
_bytes_suffix db ' bytes.',0Dh,0Ah,0

_counter db 4,'0000'

;align 4

memory_start dd ?
memory_end dd ?
additional_memory dd ?
additional_memory_end dd ?
input_file dd ?
output_file dd ?
source_start dd ?
code_start dd ?
code_size dd ?
real_code_size dd ?
start_time dd ?
written_size dd ?

str_src dd ?
str_src_len dd ?

current_line dd ?
macros_list dd ?
macro_constants dd ?
macro_block dd ?
macro_block_line_number dd ?
struc_name dd ?
current_locals_prefix dd ?
labels_list dd ?
label_hash dd ?
org_start dd ?
org_sib dd ?
undefined_data_start dd ?
undefined_data_end dd ?
counter dd ?
counter_limit dd ?
error_line dd ?
error dd ?
display_buffer dd ?
structures_buffer dd ?
number_start dd ?
current_offset dd ?
value dq ?
fp_value rd 8
symbol_identifier dd ?
format_flags dd ?
number_of_relocations dd ?
number_of_sections dd ?
stub_size dd ?
header_data dd ?
sections_data dd ?
current_section dd ?
machine dw ?
subsystem dw ?
subsystem_version dd ?

macro_status db ?
parenthesis_stack db ?
output_format db ?
code_type db ?
current_pass db ?
next_pass_needed db ?
reloc_labels db ?
times_working db ?
virtual_data db ?
fp_sign db ?
fp_format db ?
value_size db ?
forced_size db ?
value_type db ?
address_size db ?
compare_type db ?
base_code db ?
extended_code db ?
postbyte_register db ?
segment_register db ?
operand_size db ?
imm_sized db ?
jump_type db ?
mmx_size db ?
mmx_prefix db ?
nextbyte db ?

characters rb 100h
converted rb 100h
params rb 100h
buffer rb 100h

;stack 4000h

