<?php

class Z_View_Helper_FormMultielement extends Zend_View_Helper_FormText
{
 
    public function FormMultielement($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        
        $elements = $attribs['multioptions'];
        $firstkey = array_keys($elements); 
        $firstkey = $firstkey[0];
        
        unset($attribs['multioptions']);
        
        $header = '';
        foreach ($elements as $el)
        {
        	$header .= '<th colspan="2">'.$el['label'].'</th>';
        }
        $header = '<table><tr>'.$header.'</tr>';
        
        $footer = '</table>';        
        $count=0;
        if(isset($value[$firstkey]))
			$count = count($value[$firstkey]);
        $element = '';
        $empty = true;
        if ($count)
        {
        	$empty = false;
        	$addedcount=0;
	        for ($i=0; $i<$count; $i++)
	        {
	        	$setted=false;
	        	foreach ($elements as $key=>$el) 
	        		if (isset($value[$key][$i]) && trim($value[$key][$i])) 
	        			$setted = true;
	        	
	        	if ($setted)
	        	{
	        		$addedcount++;
	        		$subel = '';

		        	foreach ($elements as $key=>$val)
		        	{
		        		if ($val['_type']=='text')
		        		{
		        			$val['id'] = $name.'-'.$i.'-'.$key;
		        			$subel .= '<td>'.$this->view->formText($name.'['.$key.'][]',trim($value[$key][$i]),$val).'</td>'."\n";
		        			$subel.='<td><span id="delete">Удалить</span></td>'."\n";
		        		}
		        		elseif ($val['_type']=='textarea')
		        		{
		        			$val['id'] = $name.'-'.$i.'-'.$key;
		        			$subel .= '<td>'.$this->view->formTextarea($name.'['.$key.'][]',trim($value[$key][$i]),$val).'</td>'."\n";
		        			$subel.='<td><span id="delete">Удалить</span></td>'."\n";
		        		}
		        	}
		        	$subel = "<tr>\n".$subel."</tr>\n";
	        		
		        	$element .= $subel;
	        	}
	        	
	        }
	        if (!$addedcount) $empty = true;
        }
        
        
        if ($empty)
        {
        	foreach ($elements as $key=>$val)
        	{
        		if ($val['_type']=='text')
        		{
        			$val['id'] = $name.'-0-'.$key;
        			$element .= '<td>'.$this->view->formText($name.'['.$key.'][]','',$val).'</td>'."\n";
        			$element.='<td><a href="#" id="delete">Удалить</a></td>'."\n";
        		}
		    	elseif ($val['_type']=='textarea')
		    	{
		    		$val['id'] = $name.'-0-'.$key;
		    		$element .= '<td>'.$this->view->formTextarea($name.'['.$key.'][]','',$val).'</td>'."\n";
		    		$element.='<td><span id="delete">Удалить</span></td>'."\n";
		    	}
        	}
        	
        	$element = "<tr>\n".$element."</tr>\n";
        }
        $js='';
        $js.=
        '$(function() {
        	$("a#add").click(function(){
    			var str = $(this).parent().parent().find("table tr").get(1);
	    		$(this).parent().parent().find("table").append("<tr>"+$(str).html()+"</tr>");
    			var laststr = $(this).parent().parent().find("table tr").get(-1);
    			$(laststr).find("input").attr("value","")
	    		return false;
        	});
        	$("a#delete").live("click",function(){
        	//alert ($(this).parent().parent().parent().parent().parent().get(0).tagName);
       		if ($(this).parent().parent().parent().parent().parent().find("table tr").length>2){
        		$(this).parent().parent().remove(); 
       		}
      		 return false; 
   		 });	
    	});';
        jQuery::evalScript($js);
        $style='';
       	if (isset($attribs['style'])&&trim($attribs['style']))
       	{
       		$style=$attribs['style'];
       	}
       	
        $xhtml =$style.$header.$element.$footer.'<p class="add_string"><a href="#" id="add">Добавить строку</a></p>';
                
        return $xhtml;
    }
}
