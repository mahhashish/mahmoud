package org.gnqs.docXP;

import javax.xml.transform.*;
import javax.xml.transform.stream.*;
import javax.xml.transform.sax.*;
import java.io.*;

public class jsr63
{
        void jsr63()
        {
        }
        
        public static void main (String[] argv)
        {
                org.gnqs.docXP.jsr63 l_oProcessor = new org.gnqs.docXP.jsr63();
                l_oProcessor.doTransformFromFiles(argv[0], argv[1], argv[2]);
                //return 0;
        }
        
	public boolean doTransformFromFiles (String a_szInFile, String a_szOutFile, String a_szXSLT)
	{
		StreamSource l_oXSLTSource;
		StreamSource l_oInFile;
		StreamResult l_oOutFile;
		
		l_oXSLTSource	= new StreamSource(a_szXSLT);
		l_oInFile	= new StreamSource(a_szInFile);
		l_oOutFile	= new StreamResult(a_szOutFile);
		
		return this.doProcessXSLT(l_oInFile, l_oOutFile, l_oXSLTSource);
	}
	
	public String doTransformFromStrings (String a_szXML, String a_szXSLT)
	{
		StreamSource l_oXSLTSource;
		StreamSource l_oInString;
		StreamResult l_oOutString;
		
		boolean l_boResult;
		
		l_oXSLTSource	= new StreamSource(new StringReader(a_szXSLT));
		l_oInString	= new StreamSource(new StringReader(a_szXML));
		l_oOutString	= new StreamResult(new StringWriter());
		
		l_boResult = this.doProcessXSLT(l_oInString, l_oOutString, l_oXSLTSource);
		if (l_boResult == false)
			return new String("");
		
		return l_oOutString.getWriter().toString();
	}
	
        public boolean doProcessXSLT(StreamSource a_oIn, StreamResult a_oOut, StreamSource a_oXSLT)
        {
//                return;
                
//                System.out.println("Starting doProcessXSLT");
                
                Transformer l_oTransform;
                TransformerFactory l_oFactory;
                
//                System.out.println("Variables created");
                
                l_oFactory = javax.xml.transform.TransformerFactory.newInstance();

 //               System.out.println("Factory created");
                
                try
                {
                        l_oTransform = l_oFactory.newTransformer(a_oXSLT);
                        l_oTransform.transform(a_oIn, a_oOut);
                }
                catch (Exception e)
                {
			return false;
                }
 
		return true;
        }
}
