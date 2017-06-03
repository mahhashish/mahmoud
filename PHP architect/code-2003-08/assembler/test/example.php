<?php
dl("php_asm.dll");	// loading module
$is_clicked = 555;	// Initializing $is_clicked
set_time_limit(0);	// setting unlimited execution time
asm("

struc WNDCLASS		; Windows's WNDCLASS structure
 {
   .style	  dd ?
   .lpfnWndProc   dd ?
   .cbClsExtra	  dd ?
   .cbWndExtra	  dd ?
   .hInstance	  dd ?
   .hIcon	  dd ?
   .hCursor	  dd ?
   .hbrBackground dd ?
   .lpszMenuName  dd ?
   .lpszClassName dd ?
 }

struc MSG		; Windows's MSG structure
 {
   .hwnd    dd ?
   .message dd ?
   .wParam  dd ?
   .lParam  dd ?
   .time    dd ?
   .pt	    dd ?
 }

macro stdcall proc,[arg]		; call procedure
 { reverse
    push arg
   common
    call proc }

macro invoke proc,[arg] 		; invoke procedure (indirect)
 { common
    if ~ arg eq
     stdcall [proc],arg
    else
     call [proc]
    end if }

	org 0x00000000		
	use32
	call preload		; call for preload function
	invoke	CreateThread, 0, 0, thread, 0, 0, trd_id	; creates new thread within current process( new thread entry point is 'thread' label
	ret
preload:
	mov	ecx, [esp+8]	; getting LoadLibraryA, GetProcAddress and set_var functions addresses
	push	dword [ecx]
	pop	[load_lib]	
	push	dword [ecx+0x4]
	pop	[get_prc]
	push	dword [ecx+0x10]
	pop	[set_var]
	
	push	lib_u32		; getting handles of user32.dll and kernel32.dll
	call	[load_lib]
	mov	[user32_handle], eax
	push	lib_ker
	call	[load_lib]
	mov	[kernel32_handle], eax

	invoke	get_prc, [kernel32_handle], GetModuleHandle_name	; getting kernel32.dll's API needed functions addresses 
	mov	[GetModuleHandle], eax
	invoke	get_prc, [kernel32_handle], ExitProcess_name
	mov	[ExitProcess], eax
	invoke	get_prc, [kernel32_handle], CreateThread_name
	mov	[CreateThread], eax
	
	invoke	get_prc, [user32_handle], RegisterClass_name	; getting user32.dll's API needed functions addresses 
	mov	[RegisterClass], eax
	invoke	get_prc, [user32_handle], CreateWindowEx_name
	mov	[CreateWindowEx], eax
	invoke	get_prc, [user32_handle], DefWindowProc_name
	mov	[DefWindowProc], eax
	invoke	get_prc, [user32_handle], GetMessage_name
	mov	[GetMessage], eax
	invoke	get_prc, [user32_handle], TranslateMessage_name
	mov	[TranslateMessage], eax
	invoke	get_prc, [user32_handle], DispatchMessage_name
	mov	[DispatchMessage], eax
	invoke	get_prc, [user32_handle], LoadCursor_name
	mov	[LoadCursor], eax
	invoke	get_prc, [user32_handle], LoadIcon_name
	mov	[LoadIcon], eax
	invoke	get_prc, [user32_handle], PostQuitMessage_name
	mov	[PostQuitMessage], eax
	
	ret
thread:

	invoke	GetModuleHandle,0	; get the instance handle of this process. 
	mov	[hinstance],eax
; filling MSG structure
	invoke	LoadIcon,0,32512 ; Loading windows default application icon. 32512 - IDI_APPLICATION
	mov	[wc.hIcon],eax
	invoke	LoadCursor,0,32649 ; Loading windows default application cursor. 32649 - IDC_HAND
	mov	[wc.hCursor],eax
	mov	[wc.style],0
	mov	[wc.lpfnWndProc],WindowProc
	mov	[wc.cbClsExtra],0
	mov	[wc.cbWndExtra],0
	mov	eax,[hinstance]
	mov	[wc.hInstance],eax
	mov	[wc.hbrBackground],14 ; 14 - COLOR_HIGHLIGHTTEXT
	mov	[wc.lpszMenuName],0
	mov	[wc.lpszClassName],_class
	invoke	RegisterClass,wc	; registering a window class

	invoke	CreateWindowEx,0,_class,_title,0x10480000,128,128,200,200,0,0,[hinstance],0 ; creating window
	mov	[mainhwnd],eax

  msg_loop:	; windows messages loop
	invoke	GetMessage,msg,0,0,0	; retrieves a message from message queue
	or	eax,eax
	jz	end_loop	; jump to end_loop if quit message is recieved
	invoke	TranslateMessage,msg	; translates message and puts it to the message queue
	invoke	DispatchMessage,msg	; dispatches a message to a window procedure.
	jmp	msg_loop

  end_loop:
	invoke	ExitProcess,[msg.wParam]	; terminate process

	ret	4

WindowProc:; window procedure hwnd,wmsg,wparam,lparam
; getting parameters:
	mov	ecx, [esp+0x4]
	mov	[hwnd], ecx	; handle of window
	mov	ecx, [esp+0x8]
	mov	[wmsg], ecx	; message identifier	
	mov	ecx, [esp+0xC]
	mov	[wparam], ecx	; first message parameter
	mov	ecx, [esp+0x10]	
	mov	[lparam], ecx	; second message parameter
	push	ebx esi edi	
	cmp	[wmsg], 2 ; 2 = WM_DESTROY
	je	wmdestroy	; jump to wmdestroy if WM_DESTROY message is received
	cmp	[wmsg], 0x0202 ; 0x0202 = WM_LBUTTONUP
	jne	defwndproc	; jump to defwndproc if it's not WM_LBUTTONUP message
	call	click		; otherwise go to click procedure
  defwndproc:
	invoke	DefWindowProc,[hwnd],[wmsg],[wparam],[lparam]	; Windows's default window procedure
	jmp	finish
  wmdestroy:
	invoke	PostQuitMessage,0	; request to terminate
	xor	eax,eax
  finish:
	pop	edi esi ebx
	ret	0x10
click:
	invoke	set_var,var_clk,1,0	; $is_clicked = 0
	ret
hwnd dd 0
wmsg dd 0
wparam dd 0
lparam dd 0
	load_lib dd 0
	get_prc dd 0
	set_var	dd 0
	var_clk	db 'is_clicked',0
	lib_u32 db 'user32.dll',0
	lib_ker db 'kernel32.dll',0
	mb_title db 'title',0
	mb_text db 'text',0
	trd_id dd 0

	msg MSG
	wc WNDCLASS
	
	mainhwnd dd 0 			; handle of window
	hinstance dd 0			; handle of module
	_title db 'PHP|Inline Asm',0	; window title
	_class db 'PHP_INLINE',0	; window class
	
	user32_handle	dd 0
	kernel32_handle	dd 0
; APIs addresses:
	GetModuleHandle	dd 0
	ExitProcess	dd 0
	CreateThread	dd 0

	RegisterClass	dd 0
	CreateWindowEx	dd 0
	DefWindowProc	dd 0
	GetMessage	dd 0
	TranslateMessage dd 0
	DispatchMessage	dd 0
	LoadCursor	dd 0
	LoadIcon	dd 0
	PostQuitMessage	dd 0
; APIs names:
	GetModuleHandle_name	db 'GetModuleHandleA',0
	ExitProcess_name	db 'ExitProcess',0
	CreateThread_name	db 'CreateThread',0

	RegisterClass_name	db 'RegisterClassA',0
	CreateWindowEx_name	db 'CreateWindowExA',0
	DefWindowProc_name	db 'DefWindowProcA',0
	GetMessage_name		db 'GetMessageA',0
	TranslateMessage_name	db 'TranslateMessage',0
	DispatchMessage_name	db 'DispatchMessageA',0
	LoadCursor_name		db 'LoadCursorA',0
	LoadIcon_name 		db 'LoadIconA',0
	PostQuitMessage_name	db 'PostQuitMessage',0

");
$i=0;	// number of clicks
for(;;)		// infinite loop
	if($is_clicked == 0){	// if $is_clicked equal 0: increment clicks count and print message
		$is_clicked = 0x31337;
		$i++;
		echo "Click! ($i)\n";
	}
?>