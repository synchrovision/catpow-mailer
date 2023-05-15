/* global console gtag ga Catpow ReactDOM React Promise*/
window.Catpow=window.Catpow || {};
Catpow.Components=Catpow.Components || {};
Catpow.UI=Catpow.UI || {};

Catpow.MailFormUrl=document.scripts[document.scripts.length-1].src;
Catpow.MailForm=function(form){
	var cmf=this;
	form.addEventListener('submit',function(e){e.preventDefault();});
	window.addEventListener('popstate',function(e){
		if(e.state.html){
			form.innerHTML=e.state.html;
			cmf.reset();
			cmf.focus();
		}
	});
	cmf.focus=function(){
		var bnd=form.getBoundingClientRect();
		window.scrollBy({top:bnd.top-100,behavior:'smooth'});
	}
	cmf.focusAlert=function(){
		var bnd=form.querySelector('.cmf-input.is-error').getBoundingClientRect();
		window.scrollBy({top:bnd.top-200,behavior:'smooth'});
	}
	cmf.send=function(data,cb=false){
		var xhr=new XMLHttpRequest();
		xhr.responseType='text';
		xhr.onload=function(){
			if(xhr.readyState===4 && xhr.status===200){
				var res;
				try{
					res=JSON.parse(xhr.response);
				}
				catch(e){
					res={error:{"@form":xhr.responseText}};
				}
				if(res.error){
					cmf.showError(res.error);
					cmf.focusAlert();
					return;
				}
				if(res.html){
					form.innerHTML=res.html;
					cmf.reset();
				}
				if(res.uri && res.uri!==window.location.pathname){
					history.pushState(res,null,res.uri);
					if(window.gtag){
						gtag('set','page_path',res.uri);
						gtag('event','page_view');
					}
					if(window.ga){
						ga('send','pageview',res.uri);
					}
				}
				if(cb){cb(res);}
			}
		};
		xhr.open('POST',Catpow.MailFormUrl);
		xhr.setRequestHeader('X-CMF-NONCE',Catpow.MailFormNonce);
		xhr.send(data);
	};
	cmf.showError=function(error){
		Object.keys(error).map(function(key){
			if(key==='@form'){cmf.alert(error[key]);}
			else{cmf.inputs[key].alert(error[key]);}
		});
	};
	cmf.getFileUrl=function(name){
		return Catpow.MailFormUrl+'?render='+name;
	};
	cmf.alert=function(text){
		var alert=form.querySelector('.cmf-form__alert');
		if(!alert){
			alert=document.createElement('div');
			alert.className='cmf-form__alert';
			form.insertBefore(alert,form.firstChild);
		}
		alert.innerHTML=text;
	};
	cmf.reset=function(){
		cmf.inputs={};
		cmf.sealedButtons=[];
		cmf.agreementCheckboxes=[];
		cmf.agreed=false;
		Array.prototype.forEach.call(form.querySelectorAll('.cmf-input'),function(input){
			cmf.inputs[input.dataset.input]=new Catpow.MailFormInput(input);
			if(input.classList.contains('cmf-agreement')){
				var checkbox=input.querySelector('input[type="checkbox"]');
				checkbox.addEventListener('change',cmf.updateState);
				cmf.agreementCheckboxes.push(checkbox);
			}
		});
		Array.prototype.forEach.call(form.querySelectorAll('.cmf-ui'),function(ui){
			const props=JSON.parse(ui.textContent);
			Object.assign(props,{cmf});
			ReactDOM.render(React.createElement(Catpow.UI[ui.dataset.ui],props),ui);
		});
		Array.prototype.forEach.call(form.querySelectorAll('.cmf-button'),function(button){
			if(button.classList.contains('sealed')){cmf.sealedButtons.push(button);}
			button.addEventListener('click',function(){
				if(button.classList.contains('disabled')){return;}
				var fd=new FormData(form);
				fd.append('action',button.dataset.action);
				cmf.send(fd,cmf.focus);
			});
		});
		cmf.updateState();
		form.dispatchEvent(cmf.resetEvent);
	};
	cmf.resetEvent=new Event('reset');
	cmf.updateState=function(){
		cmf.agreed=cmf.agreementCheckboxes.every(function(checkbox){return checkbox.checked;});
		cmf.sealedButtons.map(function(button){
			button.classList[cmf.agreed?'remove':'add']('disabled');
		});
	};
	cmf.init=function(){
		var fd=new FormData(form);
		fd.append('action','init');
		Object.keys(form.dataset).map(function(key){
			fd.append(key,form.dataset[key]);
		});
		cmf.send(fd);
	};
	cmf.init();
	return cmf;
}
Catpow.MailForm.loadScript=function(src,cb){
	var el=document.createElement('script');
	el.setAttribute('type','text/javascript');
	el.setAttribute('src',src);
	document.body.appendChild(el);
	if(cb){el.onload=cb;}
}
Catpow.MailForm.loadStyle=function(href){
	var el=document.createElement('link');
	el.setAttribute('rel','stylesheet');
	el.setAttribute('href',href);
	document.head.appendChild(el);
}
Catpow.MailFormInput=function(input){
	this.el=input;
	this.alert=function(text){
		var alert=input.querySelector('.cmf-input__alert');
		if(!alert){
			alert=document.createElement('span');
			alert.className='cmf-input__alert';
			input.insertBefore(alert,input.firstChild);
		}
		alert.innerHTML=text;
		input.classList.add('is-error');
	};
	this.hideAlert=function(){
		input.classList.remove('is-error');
	};
	input.addEventListener('change',this.hideAlert);
	return this;
}

window.addEventListener('DOMContentLoaded',function(){
	new Promise(function(resolve){
		if(!Catpow.MailForm.requireReact || 'React' in window){
			resolve();
		}
		else{
			Catpow.MailForm.loadScript('https://unpkg.com/react@17/umd/react.production.min.js',function(){
				Catpow.MailForm.loadScript('https://unpkg.com/react-dom@17/umd/react-dom.production.min.js',function(){
					resolve();
				});
			});
		}
	}).then(function(){
		new Promise(function(resolve){
			if(Catpow.MailForm.deps){
				Catpow.MailForm.deps.styles.forEach(function(style){Catpow.MailForm.loadStyle(style);});
				if(Catpow.MailForm.deps.scripts.length){
					Promise.all(Catpow.MailForm.deps.scripts.map(function(script){
						return new Promise(function(resolve){
							Catpow.MailForm.loadScript(script,resolve);
						});
					})).then(function(){
						resolve();
					});
				}
				else{
					resolve();
				}
			}
			else{
				resolve();
			}
		}).then(function(){
			Array.prototype.forEach.call(document.querySelectorAll('form.cmf-form'),Catpow.MailForm);
		});
	});
});