Catpow.UI.DateSelect=(props)=>{
	const {useState,useReducer,useMemo,useCallback,useEffect}=React;
	const {className="cmf-ui-dateselect"}=props;
	
	const getDateValue=useCallback(function(dateObj){
		return dateObj.getFullYear()+'-'+ (dateObj.getMonth()+1)+'-'+ dateObj.getDate();
	},[]);
	const getDateObject=useCallback(function(dateValue,defaultValue){
		var d=dateValue.match(/^(\d+)\-(\d+)\-(\d+)$/);
		if(d){return new Date(d[1],d[2]-1,d[3]);}
		return getRelativeDateTimeObject(dateValue,defaultValue);
	},[]);
	const getRelativeDateTimeObject=useCallback(function(dateTimeValue,defaultValue){
		if(dateTimeValue==='now'){return new Date();}
		var r=dateTimeValue.match(/^([+\-]\d+)\s+(year|week|month|day|hour|minute|second)s?/);
		if(r){
			var d=new Date();
			var rv=parseInt(r[1]);
			switch(r[2]){
				case 'year':d.setFullYear(d.getFullYear()+rv);break;
				case 'week':d.setDate(d.getDate()+rv*7);break;
				case 'month':d.setMonth(d.getMonth()+rv);break;
				case 'day':d.setDate(d.getDate()+rv);break;
				case 'hour':d.setHours(d.getHours()+rv);break;
				case 'minute':d.setMinutes(d.getMinutes()+rv);break;
				case 'second':d.setSeconds(d.getSeconds()+rv);break;
			}
			return d;
		}
		if(defaultValue){return defaultValue;}
		return false;
	},[]);
	const now=useMemo(()=>getDateObject('now'));
	
	const [state,dispatch]=useReducer((state,action)=>{
		switch(action.type){
			case 'init':{
				state.min=getDateObject(props.min || '-80 year');
				state.max=getDateObject(props.max || '+1 year');
				state.minTime=state.min.getTime();
				state.maxTime=state.max.getTime();
				state.minYear=state.min.getFullYear();
				state.maxYear=state.max.getFullYear();
				state.minMonth=1;
				state.maxMonth=12;
				state.minDate=1;
				state.maxDate=31;
				action.value=props.value;
				return {...state};
			}
			case 'update':{
				const d=action.value?(
					getDateObject(action.value)
				):(
					new Date(
						action.year || state.year || now.getFullYear(),
						(action.month || state.month || now.getMonth()+1)-1,
						action.date || state.date || now.getDate()
					)
				);
				if(isNaN(d.getTime())){
				   state.value=state.year=state.month=state.date=undefined;
					return {...state};
				}
				const t=d.getTime();
				if(t<state.minTime){d.setTime(state.minTime);}
				if(t>state.maxTime){d.setTime(state.maxTime);}
				state.value=getDateValue(d);
				state.year=d.getFullYear();
				state.month=d.getMonth()+1;
				state.date=d.getDate();
				
				if(d.getFullYear()===state.minYear){
					state.minMonth=state.min.getMonth()+1;
					if(d.getMonth()===state.minMonth-1){
						state.minDate=state.min.getDate();
					}
					else{
						state.minDate=1;
					}
				}
				else{
					state.minMonth=1;
					state.minDate=1;
				}
				if(d.getFullYear()===state.maxYear){
					state.maxMonth=state.max.getMonth()+1;
					if(d.getMonth()===state.maxMonth-1){
						state.maxDate=state.max.getDate();
					}
					else{
						state.maxDate=(new Date(d.getFullYear(),d.getMonth()+1,0)).getDate();
					}
				}
				else{
					state.maxMonth=12;
					state.maxDate=(new Date(d.getFullYear(),d.getMonth()+1,0)).getDate();
				}
				return {...state};
			}
		}
		return state;
	},{});
	useEffect(()=>dispatch({type:'init'}),[]);
	
	return (
		<div className={className}>
			<div className={className+"__inputs"}>
				<Catpow.SelectNumber label="---" min={state.minYear} max={state.maxYear} value={state.year} onChange={(year)=>{dispatch({type:'update',year})}}/>
				<span className={className+"__inputs-unit"}>年</span>
				<Catpow.SelectNumber label="---" min={state.minMonth} max={state.maxMonth} value={state.month} onChange={(month)=>{dispatch({type:'update',month})}}/>
				<span className={className+"__inputs-unit"}>月</span>
				<Catpow.SelectNumber label="---" min={state.minDate} max={state.maxDate} value={state.date} onChange={(date)=>{dispatch({type:'update',date})}}/>
				<span className={className+"__inputs-unit"}>日</span>
			</div>
			{state.value &&
				<Catpow.Components.HiddenValues
					name={props.name}
					value={state.value}
				/>
			}
		</div>
	);
}