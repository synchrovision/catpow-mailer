Catpow.UI.ZipCode=(props)=>{
	const {useCallback,useMemo,useState,useRef,useEffect}=React;
	const {className="cmf-ui-zipcode"}=props;
	const {HiddenValues}=Catpow.Components;
	const ref0=useRef();
	const ref1=useRef();
	const refs=useMemo(()=>[ref0,ref1],[ref0,ref1]);
	const [value,setValue]=useState(props.value || '-');
	const [isComposing,setIsComposing]=useState(false);
	const secs=value.split('-').slice(0,2);

	const setSec=useCallback((i,val)=>{
		if(!val.match(/^[\d\-]+$/)){val='';}
		const matches=val.match(/^(\d{3})\-?(\d{4})$/);
		if(matches){
			secs[0]=matches[1];
			secs[1]=matches[2];
		}
		else{
			secs[i]=val;
			if(i==0 && val.length>2){
				refs[1].current.focus();
			}
		}
		setValue(secs.join('-'));
	},[refs]);
	useEffect(()=>{
		if(undefined === window.AjaxZip3){return;}
		window.AjaxZip3.zip2addr(refs[0].current,refs[1].current,props.pref,props.addr);
	},[refs,value]);
	
	const Input=useCallback((props)=>{
		const {className,index,refs}=props;
		const [value,setValue]=useState(props.value);
		const [isComposing,setIsComposing]=useState(false);
		useEffect(()=>{
			if(!isComposing){setSec(index,value);}
		},[isComposing,index,value]);
		useEffect(()=>{
			setValue(props.value);
		},[props.value]);
		return (
			<input
				type="text"
				size={["3","4"][index]}
				className={className}
				onChange={(e)=>{
					setValue(e.target.value);
				}}
				onCompositionStart={(e)=>{
					setIsComposing(true);
				}}
				onCompositionEnd={(e)=>{
					setIsComposing(false);
					setValue(e.target.value);
				}}
				ref={refs[index]}
				value={value}
			/>
		);
	},[setSec]);

	return (
		<div className={className}>
			<Input className={className+"__sec0"} index={0} value={secs[0]} refs={refs}/>
			<span className={className+"__sep"}>-</span>
			<Input className={className+"__sec1"} index={1} value={secs[1]} refs={refs}/>
			{value && value!=='-' && (
				<HiddenValues
					name={props.name}
					value={value}
				/>
			)}
		</div>
	);
}