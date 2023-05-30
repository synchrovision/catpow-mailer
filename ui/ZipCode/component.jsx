Catpow.UI.ZipCode=(props)=>{
	const {useCallback,useState,useRef,useEffect}=React;
	const {className="cmf-ui-zipcode"}=props;
	const {HiddenValues}=Catpow.Components;
	const refs=[useRef(),useRef()];
	const [value,setValue]=useState(props.value || '-');
	const [isComposing,setIsComposing]=useState(false);
	const secs=value.split('-').slice(0,2);

	const setSec=useCallback((i,val,isComposing)=>{
		if(!val.match(/^[\d\-]+$/)){val='';}
		const matches=val.match(/^(\d{3})\-?(\d{4})$/);
		if(matches){
			secs[0]=matches[1];
			secs[1]=matches[2];
		}
		else{
			secs[i]=val;
			if(i==0 && val.length>2){
				if(!isComposing){
					refs[1].current.focus();
				}
			}
		}
		setValue(secs.join('-'));
	},[]);
	useEffect(()=>{
		if(undefined === window.AjaxZip3){return;}
		window.AjaxZip3.zip2addr(refs[0].current,refs[1].current,props.pref,props.addr);
	},[value]);
	
	const input=useCallback((i)=>(
		<input
			type="text"
			size={["3","4"][i]}
			className={className+"__sec"+i}
			onChange={(e)=>{
				var val=e.target.value;
				setSec(i,e.target.value,isComposing);
			}}
			onCompositionStart={(e)=>{
				setIsComposing(true);
			}}
			onCompositionEnd={(e)=>{
				setSec(i,e.target.value,isComposing);
				setIsComposing(false);
			}}
			ref={refs[i]}
			value={secs[i]}
		/>
	),[className,setSec,setIsComposing]);

	return (
		<div className={className}>
			{input(0)}
			<span className={className+"__sep"}>-</span>
			{input(1)}
			{value && value!=='-' && (
				<HiddenValues
					name={props.name}
					value={value}
				/>
			)}
		</div>
	);
}