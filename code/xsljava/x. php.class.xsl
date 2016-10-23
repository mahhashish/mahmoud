<?xml version="1.0" encoding="iso-8859-1" ?>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns="http://www.w3.org/TR/xhtml1/strict">

<xsl:output method="html"/>

<xsl:template match="/">
<html>
<head>
<title>class <xsl:value-of select="//class/@name"/></title>
<link href="style.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<xsl:apply-templates select="//class"/>
</body>
</html>
</xsl:template>

<xsl:template match="//class">
<xsl:variable name="class" select="@name"/>
<p>Navbar links : Package <a href="metadata.package.package.html#{package}" target="contents"><xsl:value-of select="package"/></a> :: Class <a href="metadata.package.package.html#{package}::{$class}" target="contents"><xsl:value-of select="$class"/></a> :: <a href="{sourcefile}">View Source</a></p>
<hr/>
<h1><xsl:value-of select="@name"/></h1>
<p class="shortcomment"><i><b><xsl:value-of select="@name"/></b> - <xsl:value-of select="shortcomment"/></i></p>

<h2>Description</h2>
<xsl:if test="comment">
  <pre><xsl:value-of select="comment"/></pre>
</xsl:if>

<xsl:if test="package">
  <h3>Package</h3>
  <ul><li><a href="metadata.package.package.html#{package}" target="contents"><xsl:value-of select="package"/></a></li></ul>
</xsl:if>

<xsl:if test="baseclass">
  <h3>Baseclass</h3>
  <ul><li><a href="php.class.{baseclass}.html"><xsl:value-of select="baseclass"/></a></li></ul>
</xsl:if>

<xsl:if test="derived[@baseclass=$class]">
  <h3>Derived Classes</h3>
  <ul>
  <xsl:for-each select="derived[@baseclass=$class]">
    <xsl:sort select="." order="ascending"/>
    <li><a href="php.class.{.}.html"><xsl:value-of select="."/></a></li>
  </xsl:for-each>
  </ul>
</xsl:if>

<h3>Public Methods</h3>
<xsl:choose>
  <xsl:when test="./functions/function[not (deprecated) and not ((access = 'private') or (access = 'protected'))]">
    <p>These are the methods that can safely be called from your code.</p>
    <ul>
    <xsl:apply-templates select="./functions/function[not (deprecated) and not ((access = 'protected') or (access = 'private'))]">
      <xsl:sort select="@name" order="ascending"/>
    </xsl:apply-templates>
    </ul>
  </xsl:when>
  <xsl:otherwise>
    <p>This class has no public methods.</p>
  </xsl:otherwise>
</xsl:choose>

<xsl:if test="./parent/function[not (deprecated) and not ((access = 'protected') or (access = 'private'))]">
  <h3>Inherited Public Methods</h3>
  <p>These methods have been inherited from the baseclass <i><xsl:value-of select="baseclass"/></i>, and any classes that the baseclass has inherited from.  You may also call these methods safely from your code.</p>
  <ul>
  <xsl:apply-templates select="./parent/function[not (deprecated) and not ((access = 'private') or (access = 'protected'))]">
    <xsl:sort select="@name" order="ascending"/>
  </xsl:apply-templates>
  </ul>
</xsl:if>

<h3>Protected Methods</h3>
<xsl:choose>
  <xsl:when test="./functions/function[not (deprecated) and (access = 'protected')]">
    <p>Protected methods are there to support the public methods of the class. <span class="warning">You should only call these methods from the class itself, or from a derived class.</span></p>
    <ul>
    <xsl:apply-templates select="./functions/function[not (deprecated) and (access = 'protected')]">
      <xsl:sort select="@name" order="ascending"/>
    </xsl:apply-templates>
    </ul>
  </xsl:when>
  <xsl:otherwise>
    <p>This class has no protected methods.</p>
  </xsl:otherwise>
</xsl:choose>

<xsl:if test="./parent/function[not (deprecated) and (access = 'protected')]">
  <h3>Inherited Protected Methods</h3>
  <p>These methods have been inherited from the baseclass <i><xsl:value-of select="baseclass"/></i>, and any classes that the baseclass has inherited from.  <span class="warning">You should only call these methods from the class itself, or from a derived class.</span></p>
  <ul>
  <xsl:apply-templates select="./parent/function[not (deprecated) and (access = 'protected')]">
    <xsl:sort select="@name" order="ascending"/>
  </xsl:apply-templates>
  </ul>
</xsl:if>

<h3>Private Methods</h3>
<xsl:choose>
  <xsl:when test="./functions/function[not (deprecated) and (access = 'private')]">
    <p>Private methods are there to support the public methods of the class. <span class="warning">You should not call these methods from outside the class.</span></p>
    <ul>
    <xsl:apply-templates select="./functions/function[not (deprecated) and (access = 'private')]">
      <xsl:sort select="@name" order="ascending"/>
    </xsl:apply-templates>
    </ul>
  </xsl:when>
  <xsl:otherwise>
    <p>This class has no private methods.</p>
  </xsl:otherwise>
</xsl:choose>

<xsl:if test="./parent/function[not (deprecated) and (access = 'private')]">
  <h3>Inherited Private Methods</h3>
  <p>These methods have been inherited from the baseclass <i><xsl:value-of select="baseclass"/></i>, and any classes that the baseclass has inherited from.  <span class="warning">You must not call these methods.  You must make sure that you do not accidentally override these methods.</span></p>
  <ul>
  <xsl:apply-templates select="./parent/function[not (deprecated) and (access = 'private')]">
    <xsl:sort select="@name" order="ascending"/>
  </xsl:apply-templates>
  </ul>
</xsl:if>

<xsl:if test="//function/deprecated">
  <h3>Deprecated Methods</h3>
  <p>The following method(s) are obsolete, no longer supported, and may be removed at any time without warning. <span class="warning">Do not use these in your code.</span></p>
  <ul>
  <xsl:apply-templates select="//function[(deprecated)]">
    <xsl:sort select="@name" order="ascending"/>
  </xsl:apply-templates>
  </ul>
</xsl:if>

<xsl:if test="./attributes/attribute[not (deprecated) and not (access = 'private')]">
<h3>Attributes</h3>
<p>This class has the following attribute(s).  <span class="warning">If you can get/set the same information by using a method, you should not directly access any attributes.</span></p>
<ul>
<xsl:apply-templates select="./attributes/attribute[not (deprecated) and not (access = 'private')]">
<xsl:sort select="@name"/>
</xsl:apply-templates>
</ul>
</xsl:if>

<xsl:if test="./attributes/attribute[(deprecated) and not (access = 'private')]">
<h3>Attributes</h3>
<p>The following attribute(s) are obsolete, no longer supported, and may be removed at any time without warning. <span class="warning">Do not use these in your code.</span></p>
<ul>
<xsl:apply-templates select="./attributes/attribute[(deprecated) and not (access = 'private')]">
<xsl:sort select="@name"/>
</xsl:apply-templates>
</ul>
</xsl:if>

<xsl:if test="./see">
<h3>See Also</h3>
<ul>
<xsl:for-each select="./see">
<xsl:variable name="selclass" select="substring-before(., '::')"/>
<xsl:variable name="selmethod" select="substring-after(., '::')"/>
<xsl:choose>
<xsl:when test="not($selclass = '')">
<xsl:choose>
<xsl:when test="not(substring-before($selmethod, '()') = '')">
<xsl:variable name="selmethod2" select="substring-before($selmethod, '()')"/>
<li><a href="./php.class.{$selclass}.html"><xsl:value-of select="$selclass"/></a>::<a href="./php.method.{$selclass}.{$selmethod2}.html"><xsl:value-of select="$selmethod2"/>()</a></li>
</xsl:when>
<xsl:otherwise>
<li><a href="./php.class.{$selclass}.html"><xsl:value-of select="$selclass"/></a>::<a href="./php.method.{$selclass}.{$selmethod}.html"><xsl:value-of select="$selmethod"/>()</a></li>
</xsl:otherwise>
</xsl:choose>
</xsl:when>
<xsl:when test="not($selmethod = '')">
<li><a href="./php.function.$selmethod.html">::<xsl:value-of select="$selmethod"/></a></li>
</xsl:when>
<xsl:otherwise>
<li><a href="./php.function.{.}.html">::<xsl:value-of select="."/></a></li>
</xsl:otherwise>
</xsl:choose>
</xsl:for-each>
</ul>
</xsl:if>

<xsl:if test="./link">
<h3>Links</h3>
<ul>
<xsl:for-each select="./link">
<xsl:variable name="href" select="substring-before(normalize-space(.), ' ')"/>
<xsl:variable name="text" select="substring-after(normalize-space(.), $href)"/>
<li><a href="{$href}"><xsl:value-of select="$text"/></a></li>
</xsl:for-each>
</ul>
</xsl:if>

<xsl:if test="./since">
<h3>Available Since</h3>
<ul>
<xsl:for-each select="./since">
<li><xsl:value-of select="."/></li>
</xsl:for-each>
</ul>
</xsl:if>

<xsl:if test="./lastchanged">
<h3>Last Changed</h3>
<ul>
<xsl:for-each select="./lastchanged">
<li><xsl:value-of select="."/></li>
</xsl:for-each>
</ul>
</xsl:if>

<xsl:if test="copyright">
  <h3>Copyright</h3>
  <ul>
  <xsl:for-each select="copyright">
    <li><xsl:value-of select="."/></li>
  </xsl:for-each>
  </ul>
</xsl:if>

<xsl:if test="author">
  <h3>Author</h3>
  <ul>
  <xsl:for-each select="author">
    <xsl:sort select="." order="ascending"/>
    <li><xsl:value-of select="."/></li>
  </xsl:for-each>
  </ul>
</xsl:if>

<xsl:if test="//fixme">
  <h2>Outstanding Work</h2>
  <ul>
  <xsl:for-each select="//fixme">
    <xsl:sort select="parent::*/@name" order="ascending"/>
    <xsl:variable name="methodname" select="parent::*/@name"/>
    <xsl:choose>
    <xsl:when test="parent::function">
    <li><a href="php.method.{$class}.{$methodname}.html"><xsl:value-of select="$methodname"/>()</a> - <xsl:value-of select="."/></li>
    </xsl:when>
    <xsl:when test="parent::attribute">
    <li><a href="php.attribute.{$class}.{$methodname}.html">$<xsl:value-of select="$methodname"/></a> - <xsl:value-of select="."/></li>
    </xsl:when>
    <xsl:when test="parent::class">
    <li><a href="php.class.{$class}.html"><xsl:value-of select="$methodname"/>::</a> - <xsl:value-of select="."/></li>
    </xsl:when>
    </xsl:choose>
  </xsl:for-each>
  </ul>
</xsl:if>

<xsl:if test="//closedbug">
  <h2>Closed Bugs</h2>
  <ul>
  <xsl:for-each select="//closedbug">
    <xsl:sort select="parent::*/@name" order="ascending"/>
    <xsl:variable name="methodname" select="parent::*/@name"/>
    <xsl:choose>
    <xsl:when test="parent::function">
    <li><a href="php.method.{$class}.{$methodname}.html"><xsl:value-of select="$methodname"/>()</a> - <xsl:value-of select="."/></li>
    </xsl:when>
    <xsl:when test="parent::attribute">
    <li><a href="php.attribute.{$class}.{$methodname}.html">$<xsl:value-of select="$methodname"/></a> - <xsl:value-of select="."/></li>
    </xsl:when>
    <xsl:when test="parent::class">
    <li><a href="php.class.{$class}.html"><xsl:value-of select="$methodname"/>::</a> - <xsl:value-of select="."/></li>
    </xsl:when>
    </xsl:choose>
  </xsl:for-each>
  </ul>
</xsl:if>

</xsl:template>

<xsl:template match="//function">
    <li><a href="php.method.{@class}.{@name}.html"><xsl:value-of select="@name"/>()</a> - <xsl:value-of select="./shortcomment"/></li>
</xsl:template>

<xsl:template match="//attribute">
    <li><a href="php.attribute.{@class}.{@name}.html"><xsl:value-of select="@name"/></a> - <xsl:value-of select="./shortcomment"/></li>
</xsl:template>

</xsl:stylesheet>
