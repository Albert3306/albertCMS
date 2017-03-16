<?php
namespace Admin\Controller;

/**
 * 后端首页控制器
 */
class IndexController extends AdminController
{
    /**
     * 后端首页
     */
    public function index()
    {
        if(IS_POST){
            $count_day=I('post.count_day', C('COUNT_DAY'),'intval',7);
            if(D('Config')->where(array('name'=>'COUNT_DAY'))->setField('value',$count_day)===false){
                $this->error('设置失败。');
            }else{
               S('DB_CONFIG_DATA',null);
                $this->success('设置成功。','refresh');
            }
        }else{
            $users_db  = D('Common/Users');
            $today     = date('Y-m-d', time());
            $today     = strtotime($today);
            $count_day = C('COUNT_DAY',null,7);
            $count['count_day']=$count_day;

            for ($i = $count_day; $i--; $i >= 0) {
                $day = $today - $i * 86400;
                $day_after = $today - ($i - 1) * 86400;
                $week_map  = array(
                                'Mon' => '周一',
                                'Tue' => '周二',
                                'Wed' => '周三',
                                'Thu' => '周四',
                                'Fri' => '周五',
                                'Sat' => '<strong>周六</strong>',
                                'Sun' => '<strong>周日</strong>'
                            );
                $week[] = date('m月d日 ', $day). $week_map[date('D',$day)];
                $user   = $users_db->where('status=1 and reg_time >=' . $day . ' and reg_time < ' . $day_after)->count() * 1;
                $usersCount[] = $user;
                if ($i == 0) {
                    $count['today_user'] = $user;
                }
            }
            $week = json_encode($week);
            $this->assign('week', $week);
            $count['total_user'] = $userCount = $users_db->where(array('status' => 1))->count();
            $count['today_action_log'] = M('ActionLog')->where('status=1 and create_time>=' . $today)->count();
            $count['last_day']['days'] = $week;
            $count['last_day']['data'] = json_encode($usersCount);

            $this->assign('count', $count);
            $this->display();
        }
    }
}