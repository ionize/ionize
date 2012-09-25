<h1>Module Search</h1>
<p>
	This module adds Search capabilities in articles of your website<br/>
</p>
<p>Features :</p>
<ul>
	<li>Display one simple search form input,</li>
	<li>Search in articles which has the flag "indexed" set,</li>
	<li>Displays the results and link to the content, using the "main parent" page if the link points to one article.</li>
</ul>

<hr/>

<h2>Search form</h2>

<pre>
&lt;ion:search:form />
</pre>

<!-- Display the search form -->
<ion:search:form />

<h2>Search results</h2>

<pre>
&lt;!-- No search started or realm = '' -->
&lt;ion:search:realm expression="==NULL">
    &lt;p class="red">Search not started&lt;/p>
&lt;/ion:search:realm>

&lt;!-- Search launched -->
&lt;ion:search:realm expression="!=''">
    &lt;!-- No results -->
    &lt;ion:search:results:count is="0">
        &lt;p class="red">No results found for &lt;b>&lt;ion:search:realm />&lt;/b>&lt;/p>
    &lt;/ion:search:results:count>
    &lt;!-- Results found -->
    &lt;ion:else>
        &lt;p>Number of results found for &lt;b>&lt;ion:search:realm />&lt;/b> : &lt;b> &lt;ion:search:results:count />&lt;/b>&lt;/p>
    &lt;/ion:else>
&lt;/ion:search:realm>

&lt;!-- Display results -->
&lt;div id="search-results">
    &lt;ion:search:results>
        &lt;ion:result>
            &lt;ion:url href="true" tag="h3 "/>
            &lt;ion:nb_words tag="p" prefix="Nb occurrences found : "/>
            &lt;ion:content words="25" />
        &lt;/ion:result>
    &lt;/ion:search:results>
&lt;/div>
</pre>

<!-- No search started or realm = '' -->
<ion:search:realm expression="==NULL">
	<p class="red">Search not started</p>
</ion:search:realm>

<!-- Search launched -->
<ion:search:realm expression="!=''">
	<!-- No results -->
	<ion:search:results:count is="0">
		<p class="red">No results found for <b><ion:search:realm /></b></p>
	</ion:search:results:count>
	<!-- Results found -->
	<ion:else>
        <p>Number of results found for <b><ion:search:realm /></b> : <b> <ion:search:results:count /></b></p>
	</ion:else>
</ion:search:realm>

<!-- Display results -->
<div id="search-results">
	<ion:search:results>
		<ion:result>
			<ion:url href="true" tag="h3 "/>
			<ion:nb_words tag="p" prefix="Nb occurrences found : "/>
			<ion:content words="25" />
		</ion:result>
	</ion:search:results>
</div>


