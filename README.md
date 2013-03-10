Tiny PHP Framework
====

Tiny PHP framework 主要是我在做游戏项目沉淀积累。
我参考了很多游戏的框架,例如doitphp,Cascade.

controller层,比较easy,主要是将我们项目中用到的Cascade引入,cascade的优点如下：

1. 对各类数据源的访问同一接口
2. 对memcache的访问直接用如下方法
    
    直接在gateway层添加callSessionBefore或callSessionAfter,AOP思想,实现调用数据库数据与缓存数据分离。

    public function callSessionAfter($method,$args,$result) {
        switch ($method) {
            case 'execute' :
                $cache_session = $this->cache_session;

                if ($cache_session !== null && $args[1] == 'insert' && !empty($args[2]['qid']) && !empty($args[2]['user_id'])) {
                    $key = $args[2]['user_id'].'_'.$args[2]['qid'].'_got_reward';

                    $v = $cache_session->get($key);
                    if(empty($v[0])){

                        //                      $start_time = strtotime("now");
                        //                      $end_time = strtotime(date('Y-m-d').' 23:59:59');
                        //                      $time = abs($end_time - $start_time);

                        $cache_session->set($key, true, COUNTRY_POINT_QUARTER_CYCLE_TIME);
                    }   

                }   

                break;
            default :
                break;
        }   
        return null;
    } 
    
3. 分库分表
4. 并非一般MVC结构,新增一层service,可提供不同的应用接口
