<html>
	<head>
	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
	<style>
		html{
			height:100%;

		}
		#ov{
				width:20%;

			}
		#f{
			width:100%;
			z-index:0;
		}
		body{
			overflow:hidden;
			background-color:#edeff1;
		}
		#iframe_loading{
			width:100%;
			height:100%;
			background-color:#BDBDBD;
			position:absolute;
			z-index:15000;
			top:0px;
			left:0px;
		}
		.loader {
			border: 16px solid #f3f3f3; /* Light grey */
			border-top: 16px solid #a0a0a0; /* Blue */
			border-radius: 50%;
			width: 120px;
			height: 120px;
			position: absolute;
			margin: auto;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			animation: spin 2s linear infinite;
			//margin-left:45%;
			//vertical-align:middle;
			margin:auto;

		}

		@keyframes spin {
			0% { transform: rotate(0deg); }
			100% { transform: rotate(360deg); }
		}
	</style>
	</head>
	<body>

	<iframe id="ov" src="conf_Status.php" style="height:100%;  float:left; position:static; display:none;" scrolling="no" frameborder="0" > </iframe>
	<iframe id="f" style="  float:left;height:100%; " frameborder="0" src="" scrolling="yes" ></iframe>
	<div id="iframe_loading">
		<div class="loader">
		</div>
	</div>
	<?php
	$t="";
	
	?>
	<script>
	
	//global variables
	var pageType = false//return false if the page is "enclosure configuration", true if the page is "blade configuration"
	var linkArr //array that contains the links of the pages "enclosure configuration" or "blade configuration"
	var type="<?php if(isset($_GET['type'])){echo $_GET['type'];} ?>"//the type of the blade(es. MicroBlade)
	var encSel=""//the model of the selected enclosure
	var nextPage//the next page that will be open
	var closePage=false//true if the user press the button close of the aside
	var restarted=false//true if the page is restarted
	var possible=true
	var pages={ MicroBlade:{
							enclosure:"https://dev.sysgen.de/microblade-enclosures-de/",
							blade:"https://dev.sysgen.de/microblade-modules-de/",
							},
				GPUXeonPhiBlades:{
							enclosure:"https://dev.sysgen.de/gpu-xeon-phi-blade-enclosures/",
							blade:"https://dev.sysgen.de/gpu-xeon-phi-blade-modules/",
							},
				TwinBlades:{
							enclosure:"https://dev.sysgen.de/twinblade-enclosures/",
							blade:"https://dev.sysgen.de/twinblade-modules/",
							},
				OfficeBlade:{
							enclosure:"https://dev.sysgen.de/officeblade-enclosures/",
							blade:"https://dev.sysgen.de/officeblade-modules/",
							},
				DataCenterBlade:{
							enclosure:"https://dev.sysgen.de/datacenterblade-enclosures/",
							blade:"https://dev.sysgen.de/datacenterblade-modules/",
							}

				}
				
				
	if(type=="")
		location.replace(window.location.href+"?type="+prompt("You have forgotten to add the blade's type into the URL!! Add it below"))
	
	document.getElementById("f").src=pages[type]["enclosure"]
	
	//Main Function JQuery
	
	$(function () {
		
		
			
		//run when the overview frame is completely loaded
		$( "#ov" ).load(function(){
			
			// call the function setConfInit()
			
			//if the user click on "Angebotsanfrage" he will be redirect to the correct page
			$( "#ov" ).contents().find("#finalize").on("click",function(){
				
				
				//create the dialog "action no reversible"
					var a=$("#f").contents()
					a.find('#custom_tooltip .object-container').html("<p>The action is not reversible, are you sure? </p> <hr style='margin-top:40px;' /><div id='action'><a onclick='closePopUp(\"custom_tooltip \");'  style='' class='ty-btn ty-btn__secondary'>No</a><a id='continue' style='float:right;'class='ty-btn ty-btn__primary'>Yes</a></div>");
					a.find('#custom_popup_header').html("Attention !")
					a.find('.ui-dialog-titlebar-close').remove()
								//if the response is yes the user will be redirected to the correct page
								a.find("#continue").click(function(){
										location.replace("https://dev.sysgen.de/index.php?dispatch=checkout.checkout")
									});
								
									document.getElementById("f").contentWindow.createPopUp("custom_tooltip");
			});
			
			
			
			$( "#ov" ).contents().find("#restart").on("click",function(){
				document.getElementById("f").src='https://dev.sysgen.de/index.php?dispatch=checkout.clear'
				restarted=true
			});
			$( "#ov" ).contents().find("#close").on("click",function(){
				document.getElementById("f").src='https://dev.sysgen.de/index.php?dispatch=checkout.clear'
			});
			
		});



		$( "#f" ).load(
			function () {
				//the content of the iframe
				var a=$("#f").contents()
				
			
				
				
				//when a body's element is clicked,the function checks if it's an <a> object,and if it is, checks if the <a> link is allowed
				$("#f").contents().find("body").click(function(event){
							if(event.target.tagName=="A"){
								nextPage=event.target.href;
								var c= nextPage.includes(pages[type]["enclosure"]) || nextPage.includes(pages[type]["blade"]) || isPermitted(nextPage) || nextPage=='https://dev.sysgen.de/index.php?dispatch=checkout.clear' || nextPage==''
								if(!(c)){
									event.preventDefault()
									
									//create the dialog "You are leaving the configurator..."
									a.find('#custom_tooltip .object-container').html("<p>You are leaving the configurator, are you sure? </p> <hr style='margin-top:40px;' /><div id='action'><a onclick='closePopUp(\"custom_tooltip \");'  style='' class='ty-btn ty-btn__secondary'>No</a><a id='continue' style='float:right;'class='ty-btn ty-btn__primary'>Yes</a></div>");
									a.find('#custom_popup_header').html("Oops...!")
									a.find('.ui-dialog-titlebar-close').remove()
								//if the response is yes the user will be redirected to the correct page
								a.find("#continue").click(function(){
										location.replace(nextPage)
									});
								
									document.getElementById("f").contentWindow.createPopUp("custom_tooltip");
								}else{
									$("#f").src=nextPage;
								}
							}
						
					});
					
					
					//the overview is refreshed when an element is deleted by the user from the list
					
					$("#f").contents().find(".ty-cart-items__list").click(function(event){
						//console.log(event.target.className)
						if(event.target.className=="ty-icon-cancel-circle")
							
							//check if the dialog exist
							var checkExist = setInterval(function() {
								//console.log("entro!");
								if ($('#f').contents().find(".alert-success").length==1) {
								//console.log("Exists!");
								displayOverview()
								clearInterval(checkExist);
								}
							}, 50);
					})
					
				
				//show the loading icon when the page is unloaded
				document.getElementById("f").contentWindow.onbeforeunload = function () { $("#iframe_loading").show(); console.log("passed")};
				
				//create the initial popup
				//Create content inside the already existing Popup container
				a.find('#custom_tooltip .object-container').html("<p>Your cart isn´t empty! Do you want to complete the current configuration? </p> <hr style='margin-top:40px;' /><div id='action'><a href='https://dev.sysgen.de/index.php?dispatch=checkout.clear'  style='float:left;width:100px;' class='ty-btn ty-btn__secondary'>Clean the cart</a><a id='keepConf' onclick='closePopUp(\"custom_tooltip \");' style='float:right;width:100px;'class='ty-btn ty-btn__secondary'>Continue</a><a id='continue' href='#'  style='float: left;position: relative;left: 26%; margin-top: 3%;width:104px;' class='ty-btn ty-btn__primary'>Complete it!</a></div>");
				a.find('#custom_popup_header').html("Oops...!")
				a.find('.ui-dialog-titlebar-close').remove()
				a.find("#continue").click(function(){
					location.replace("https://dev.sysgen.de/index.php?dispatch=checkout.checkout")
				});
				
				
				//CHECK CART CONTENT	
				
				a.find("#keepConf").click(function(){
				if(!pageType){
				//get the encolsure´s model from the page content
				var prodId=$( "#f" ).contents().find("[id^='product_code_']").text()
				encSel=prodId
				
				//change the href property of "Choose Blade" with the filtered link
				a.find("#keepConf").attr("href", bladeFilter(prodId))
				
				//change the icon on the sidebar
				a.find("#keepConf").click(function(){
					changeEn()
					//add the number of blades in the cart to the sidebar
					var c= getMaxSubCount();
					$('#ov').contents().find("#num").html("("+c['current']+"/"+c['max']+")")
				});
			}
				});
				
				
				//display the "configuration status" sidebar
				displayOverview()
				
				//prompt("",document.getElementById("f").contentWindow.location.href)

				
				//the following actions are executed depending on the page that is being displayed on the iframe
				if(document.getElementById("f").contentWindow.location.href ==pages[type]["enclosure"]){
					
					//if the cart is not empty display the initialPopup
					if($("#f").contents().find(".ty-cart-items__empty").length ==0 )
						document.getElementById("f").contentWindow.createPopUp("custom_tooltip")
					
					//remove the sidebar
					//a.find(".span4")[0].style.display="none"
					
					//add the "enclosure configuration" pages' links to linkArr
					var nlink=a.find("[id*='add_to_cart_update_']")
					linkArr = new Array(nlink.length)
					var temp
					for(var i=0; i<nlink.length;i++){
						temp=nlink[i].getElementsByTagName("a")[0]
						linkArr[i]=temp.href
						}
						
					}
				
				//if the page is contained into the available pages' array and it is "blade configuration do..."
				else if(pageType && isPermitted(document.getElementById("f").contentWindow.location.href)){
					
					//change the content of the button 
					var btnDialog=$("#f").contents().find("[id^='button_cart_comment_']");
					btnDialog.html("Add to cart")

				}
				
				//if the page is "clean cart" the user is redirected to the main page
				else if(document.getElementById("f").contentWindow.location.href =="https://dev.sysgen.de/index.php?dispatch=checkout.cart"){					
						//console.log("closed:"+closePage)
						console.log("restarted"+restarted)
						if(closePage)
							location.replace(pages[type]["enclosure"]);
						else if(restarted)
							location.reload();
						else
							window.history.back();
				
				}
				
				//if the page is contained into the available pages' array and it is "enclosure configuration do..."
				else if(!pageType && isPermitted(document.getElementById("f").contentWindow.location.href)){
					
					//change the content of the button 
					var btnDialog=$("#f").contents().find("[id^='button_cart_comment_']");
					btnDialog.html("Add to cart")
					a.find(".ty-qty" ).remove()
					
				}else if(document.getElementById("f").contentWindow.location.href.includes(pages[type]["enclosure"])){

				}else if(document.getElementById("f").contentWindow.location.href.includes(pages[type]["blade"])){
					
					var nlink=a.find("[id*='add_to_cart_update_']")
					
					//add the "blade configuration" pages' links to linkArr
					linkArr = new Array(nlink.length)
					var temp
					for(var i=0; i<nlink.length;i++){
						temp=nlink[i].getElementsByTagName("a")[0]
						linkArr[i]=temp.href
						$(document.getElementById("f").contentWindow.document).on('click', temp, function(){
							pageType=true
						});


					}
					
					//remove the enclosure filter
					$("#f").contents().find(".ty-product-filters__block")[0].remove()
				}
				/*else if(document.getElementById("f").contentWindow.location.href=='https://dev.sysgen.de/index.php?dispatch=checkout.clear'){
					if(restarted)
						location.reload();
				}*/
				

				//hide the loading icon
					$("#iframe_loading").hide()

				});

			});

		//function that changes Enclosures step icon
		var cont= true
		function changeEn(){
			if(cont){
				$("#ov").contents().find("#Enclim").remove()
				$("#ov").contents().find("#Encl").prepend("<svg  viewBox='0 0 24 24'><path fill='#000000' d='M19,19H5V5H15V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V11H19M7.91,10.08L6.5,11.5L11,16L21,6L19.59,4.58L11,13.17L7.91,10.08Z' /></svg>")
				cont=false
			}
		}

		//function that changes Blades step icon
		var cont1= true
		function changeBl(){
			if(cont1){
				$("#ov").contents().find("#Bladim").remove()
				$("#ov").contents().find("#Blad").prepend("<svg  viewBox='0 0 24 24'><path fill='#000000' d='M19,19H5V5H15V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V11H19M7.91,10.08L6.5,11.5L11,16L21,6L19.59,4.58L11,13.17L7.91,10.08Z' /></svg>")
				cont1=false
			}
		}
		/*
		@param link the link to check
		@return true if the page is contained in linkArr
		*/
		
		function isPermitted(link){
			var perm=false
			for(var i=0; i<linkArr.length;i++){
				perm= perm || (link ==linkArr[i])
			}
			return perm
		}
		
		//function that filters the blades after enclosure's choice
		//@param model, the model of the selected enclosure
		//May delete the filter´s options
		function bladeFilter(model){
			var d={product_code: model, product_type: "blade"}
			var res;
			$.ajax({
				url: "https://dev.sysgen.de/bladeconfigurator/getFilterPath.php",
				type: "GET",    //meglio usare GET quando si tratta di sola lettura
				data: d,    //in questo caso non è necessario passare dati
				dataType: "html",
				async: false,//se fosse semplice testo userei dataType: "html"
				success: function(result){
					var cont=jQuery.parseJSON(result)["content"]
					res= pages[type]["blade"]+"?sef_rewrite=1&features_hash="+cont
					if(cont=="")
						res=pages[type]["blade"]
						},
				error: function(){
					alert("failed")
				}
				});
				return res
			}
		
		//the folowing action are executed when a dialog is displayed
		$(document).on('notificationAddToCart', function () {
			
			displayOverview();
			
			//if the page is contained in the array of the aviable pages and is "enclosure configuration"
			if(!pageType && isPermitted(document.getElementById("f").contentWindow.location.href)){
				
				$( "#f" ).contents().find(".ty-product-notification__product-name")[0].href="#"
				//add the "Choose Blade" buttton
				$( "#f" ).contents().find(".cm-notification-content").find(".ty-btn__secondary").remove()
				var primary=$( "#f" ).contents().find(".cm-notification-content").find(".ty-btn__primary")
				primary.text("Choose Blade")
				
				//remove the close dialog button
				$( "#f" ).contents().find('.cm-notification-close').remove()
				
				//get the encolsure´s model from the page content
				var prodId=$( "#f" ).contents().find("[id^='product_code_']").text()
				encSel=prodId
				
				//change the href property of "Choose Blade" with the filtered link
				alert(bladeFilter(prodId))
				primary.attr("href", bladeFilter(prodId))
				
				//change the icon on the sidebar
				primary.click(function(){
					changeEn()
					//add the number of blades in the cart to the sidebar
					var c= getMaxSubCount();
					$('#ov').contents().find("#num").html("("+c['current']+"/"+c['max']+")")
				});

			}
			
			//if the page is contained into the aviable pages'array and the page is "blade configuration"
			else if(pageType && isPermitted(document.getElementById("f").contentWindow.location.href)){
				
				$( "#f" ).contents().find(".ty-product-notification__product-name")[0].href="#"
				//add the buttons "End" and "Add Another Blade"
				var secondary=$( "#f" ).contents().find(".cm-notification-content").find(".ty-btn__secondary")
				secondary.text("End")
				secondary.attr("")
				secondary.click(function(){
					changeBl()
					location.replace("../index.php?dispatch=checkout.checkout")
				});
				
				//check if the user adds the maximum blade's number and delete the "Add Another Blade" button
				var c=getMaxSubCount();
				if(c['max']>=c['current']){
					var primary=$( "#f" ).contents().find(".cm-notification-content").find(".ty-btn__primary")
					primary.text("Add Another Blade")

					primary.attr("href", bladeFilter(encSel))
					}
					else {
						$( "#f" ).contents().find(".cm-notification-content").find(".ty-btn__primary").remove();
						$( "#f" ).contents().find(".ty-product-notification__body .clearfix")[3].append("You have selected "+(c['current']-c['max'])+" items more than enclosure can support")

					}
					$('#ov').contents().find("#num").html("("+c['current']+"/"+c['max']+")")
				}

		});
			
			//display the sidebar
			function displayOverview(){
				
				//add the components into the sidebar
				var a=$( "#f" ).contents()
				var itemList=a.find(".ty-cart-items__list").clone()
				var as=itemList.find("a")
				//var as=itemList.find(".ty-cart-items__list-item-desc a")
				for(var i=0;i<as.length;i++)
					as[i].href="#"
				$( "#ov" ).contents().find(".ty-cart-items__list").remove()
				$( "#ov" ).contents().find("#sep").remove()
				$( "#ov" ).contents().find("#elenco").append(itemList)
				$( "#ov" ).contents().find("#elenco").append("<hr id='sep'/>")
				/*$( "#ov" ).contents().find(".ty-cart-items__list-item").mouseover(function(event){
					event.target.find(".cm-cart-item-delete").show()
				})*/
				//change the property of the other page's element to get enough space
				document.getElementById('ov').style.display="initial"
				document.getElementById('f').style.width="80%"
				document.getElementById('iframe_loading').style.width="80%"
				document.getElementById('iframe_loading').style.left="20%"
			}
			
			//@return true if the user adds the maximum blade number 
			function getMaxSubCount(){
			var d={product_type: "blade"}
			var res;
			$.ajax({
				url: "https://dev.sysgen.de/bladeconfigurator/getMaxSubCount.php",
				type: "GET",    
				data: d,    
				dataType: "html",
				async: false,
				success: function(result){
					res=jQuery.parseJSON(result)
					//console.log("ocio")
						},
				error: function(){
					alert("failed")
				}
				});
				return res
			}

			//@set the session variable true when the configurator is opened 
			function setConfInit(){
			var d={status: "true"}
			$.ajax({
				url: "https://dev.sysgen.de/bladeconfigurator/toggleConfiguratorSession.php",
				type: "GET",    
				data: d,    
				dataType: "html",
				async: false,
				success: function(result){
						},
				error: function(){
					alert("failed")
				}
				});
			}

			

	</script>
</body>
</html>
