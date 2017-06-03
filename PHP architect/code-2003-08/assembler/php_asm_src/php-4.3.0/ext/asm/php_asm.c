#include "php.h"
extern void compile_asm();

#define COMPILE_DL_FIRST_MODULE 1
void ***asm_tsrm_ls;
unsigned long Func_AddressTable[5];
/* declaration of functions to be exported */

ZEND_MINIT_FUNCTION(asm);
ZEND_FUNCTION(asm);

/* compiled function list so Zend knows what's in this module */
zend_function_entry phpasm_functions[] =
{
    ZEND_FE(asm, NULL)
    {NULL, NULL, NULL}
};

/* compiled module information */
zend_module_entry phpasm_module_entry =
{
    STANDARD_MODULE_HEADER,
    "Php Inline Assembler",
    phpasm_functions,
    ZEND_MINIT(asm), 
    NULL, 
    NULL, 
    NULL, 
    NULL,
    NO_VERSION_YET,
    STANDARD_MODULE_PROPERTIES
};

/* implement standard "stub" routine to introduce ourselves to Zend */
#if COMPILE_DL_FIRST_MODULE
ZEND_GET_MODULE(phpasm)
#endif

unsigned long __stdcall get_var_type(char *name){
	
	zval **var1;																				
#ifdef ZTS
	void ***tsrm_ls;
	tsrm_ls = asm_tsrm_ls;
#endif																										
	
	if(zend_hash_find(EG(active_symbol_table), name, strlen(name)+1, (void **) &var1)==SUCCESS){
			return (*var1)->type;
	}

	return 0xffffffff;

}

unsigned long __stdcall get_var_data(char *name){
	
	zval **var1;																				
#ifdef ZTS
	void ***tsrm_ls;
	tsrm_ls = asm_tsrm_ls;
#endif																										
	
	if(zend_hash_find(EG(active_symbol_table), name, strlen(name)+1, (void **) &var1)==SUCCESS){
		switch((*var1)->type){
		case IS_NULL:
			return 0;
		case IS_LONG || IS_BOOL:
			return (*var1)->value.lval;
		case IS_STRING:
			return (unsigned long)(*var1)->value.str.val;
		}
	}

	return 0;

}

unsigned long __stdcall set_var(char *name, unsigned long type, unsigned long data){
	
	zval *var1;																				
#ifdef ZTS
	void ***tsrm_ls;
	tsrm_ls = asm_tsrm_ls;
#endif																										
	
	MAKE_STD_ZVAL(var1);
	var1->type = type;
		switch(type){
		case IS_LONG || IS_BOOL:
			var1->value.lval = data;
			break;
		case IS_STRING:
			var1->value.str.len = strlen((char *)data);
			var1->value.str.val = estrdup((char *)data);
			break;
		}

	ZEND_SET_SYMBOL(EG(active_symbol_table), name, var1);
    
	return 0;

}
/* implement function that is meant to be made available to PHP */
ZEND_MINIT_FUNCTION(asm){
	HMODULE h;

	h = GetModuleHandle("kernel32.dll");
	Func_AddressTable[0] = GetProcAddress(h,"LoadLibraryA");
	Func_AddressTable[1] = GetProcAddress(h,"GetProcAddress");
	(void*)Func_AddressTable[2] = get_var_type;
	(void*)Func_AddressTable[3] = get_var_data;
	(void*)Func_AddressTable[4] = set_var;
return SUCCESS;
}

ZEND_FUNCTION(asm)
{
    char* str2asm;
	long str2asm_len,f_call,f_res;
	void *addr_tbl;
	
#ifdef ZTS
	asm_tsrm_ls = tsrm_ls;
#endif

	addr_tbl = Func_AddressTable;
	
	f_call = (long)compile_asm;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &str2asm,&str2asm_len) == FAILURE) {
        return;
    }
	/////
	__asm{
	pushad
	mov eax, str2asm
	mov edx, str2asm_len
	mov ecx, f_call
	call ecx
	mov ebp, [esp+8]
	test eax, eax
	jz err
	push addr_tbl
	call eax
	pop edi
	mov ebp, [esp+8]
err:
	mov f_res,eax
	popad
	}



    RETURN_LONG(f_res);
}