Ionize Session Module


Installation
-------------------------------

1. Copy the whole "session" folder into your /modules directory
2. In Ionize, go to Modules > Administration
3. In the module list, click on "install" for the module called "Session"

4. Edit the file /modules/session/config/config.php and define the allowed vars.
   
   Example : 
   
   // Allow "my_var" and "my_second_var" to be read / writed by the module
   $config['module_session_allowed_variables'] = 'my_var,my_second_var';
   

Usage
-------------------------------

Display one session var : 

	<ion:session:display var="my_var" tag="h1" />


Set one session var : 

	<ion:session:set var="my_var" set="titi" />


Check for value and display content only if value matches :

	<ion:session:check var="my_var" is="toto" >
	
		<p>This will be displayed only if my_var = toto</p>
	
	</ion:session:check>


Check for value and display content only if value matches and change the value :

	<ion:session:check var="my_var" is="toto" set="titi">
	
		<p>This will be displayed only if my_var = toto</p>
		<p>And only one time, because the var will be changed</p>
	
	</ion:session:check>


Change one session variable's value through Ajax and reloads the page
(Mootools example)


	<a id="session_test" href="">Send a new value to the session var</a>
	
	<script type="text/javascript">
	
		window.addEvent('domready', function(){
		
			$('session_test').addEvent('click', function(e)
			{
				e.stop();
				
				var r = new Request({
					'url': '<ion:base_url lang="false" />session',
					data: {
						'ma_var':'tutu'
					},
					onSuccess: function(responseText, responseXML)
					{
						window.location.reload();
					}
				}).send();
			});
		});
	
	</script>



