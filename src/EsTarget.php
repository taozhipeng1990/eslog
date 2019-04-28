<?php
namespace qm;
use qm\model\Log;
use yii\log\Target;
use yii\log\Logger;
use yii\helpers\ArrayHelper;
class EsTarget extends Target{
    
    public function init(){        
    }


    public function export(){
        //日志写入
        $text =array_map([$this, 'formatMessage'], $this->messages);         
        if(is_array($text)){
            foreach($text as $item){
                $log = new Log();
                $log->msg = $item;
                $log->save();
            }
        }
    }

    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
  
        

        $level = Logger::getLevelName($level);
  
        $traces = [];
        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                $traces[] = "in {$trace['file']}:{$trace['line']}";
            }
        }

        $prefix = $this->getMessagePrefix($message);
        
        $base =[
            "time"=>$this->getTime($timestamp),
            "prefix"=>$prefix,
            "level"=>$level,
            "category"=>$category,            
            "traces"=>$traces
        ]; 
        
        if(is_string($text)){
            $base["text"] = $text;
        }else{            
            $base =array_merge($base,$text);
        }
        $base = array_filter($base);             
        return $base;
        
    }   

    protected function getContextMessage()
    {
        $context = ArrayHelper::filter($GLOBALS, $this->logVars);
        return $context;
    }    
}