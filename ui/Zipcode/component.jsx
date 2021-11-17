if(!("AjaxZip3" in window)){
	Catpow.MailForm.loadScript('https://ajaxzip3.github.io/ajaxzip3.js');
}

Catpow.UI.ZipCode=(props)=>{
	const {useCallback,useState,useRef,useEffect}=React;
	const {HiddenValues}=Catpow.Components;
	const refs=[useRef(),useRef()];
	const [value,setValue]=useState(props.value || '-');
	const [isComposing,setIsComposing]=useState(false);
	const secs=value.split('-').slice(0,2);

	const setSec=useCallback((i,val,isComposing)=>{
		if(!val.match(/^\d+$/)){val='';}
		if(val.length==7){
			setValue(val.substring(0,3)+'-'+val.substring(3));
		}
		else{
			secs[i]=val;
			if(i==0 && val.length>2){
				if(!isComposing){
					refs[1].current.focus();
				}
			}
			setValue(secs.join('-'));
		}
	},[]);
	useEffect(()=>{
		AjaxZip3.zip2addr(refs[0].current,refs[1].current,props.pref,props.addr);
	},[value]);
	
	const input=useCallback((i)=>(
		<input
			type="text"
			size={["3","4"][i]}
			className={"sec"+i}
			onChange={(e)=>{
				var val=e.target.value;
				setSec(i,e.target.value,isComposing);
			}}
			onCompositionStart={(e)=>{
				setIsComposing(true);
			}}
			onCompositionEnd={(e)=>{
				setIsComposing(false);
				setSec(i,e.target.value,isComposing);
			}}
			ref={refs[i]}
			value={secs[i]}
		/>
	));


	return (
		<div className={'ZipCode'}>
			{input(0)}
			<span class="sep">-</span>
			{input(1)}

			<HiddenValues
				name={props.name}
				value={value}
			/>
		</div>
	);
}