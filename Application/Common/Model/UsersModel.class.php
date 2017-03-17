<?php
namespace Common\Model;
use Think\Model;

/**
 * 用户模型
 */
class UsersModel extends Model{
    protected $_validate = array(
        /* 验证用户名 */
        array('username', 'checkUsernameLength', -1, self::EXISTS_VALIDATE,'callback'), // 用户名长度不合法
        array('username', 'checkDenyUsername', -2, self::EXISTS_VALIDATE, 'callback'), // 用户名禁止注册
        array('username', 'checkUsername', -12, self::EXISTS_VALIDATE, 'callback'),
        array('username', '', -3, self::EXISTS_VALIDATE, 'unique'), // 用户名被占用

        /* 验证密码 */
        array('password', '6,30', -4, self::EXISTS_VALIDATE, 'length'), // 密码长度不合法

        /* 验证邮箱 */
        array('email', 'email', -5, self::EXISTS_VALIDATE), // 邮箱格式不正确
        array('email', '4,32', -6, self::EXISTS_VALIDATE, 'length'), // 邮箱长度不合法
        array('email', 'checkDenyEmail', -7, self::EXISTS_VALIDATE, 'callback'), // 邮箱禁止注册
        array('email', '', -8, self::EXISTS_VALIDATE, 'unique'), // 邮箱被占用

        /* 验证手机号码 */
        array('mobile', '/^(1[3|4|5|8])[0-9]{9}$/', -9, self::EXISTS_VALIDATE), // 手机格式不正确 TODO:
        array('mobile', 'checkDenyMobile', -10, self::EXISTS_VALIDATE, 'callback'), // 手机禁止注册
        array('mobile', '', -11, self::EXISTS_VALIDATE, 'unique'), // 手机号被占用

        /* 验证签名 */
        array('signature', '0,100', -1, self::EXISTS_VALIDATE, 'length'),

        /* 验证昵称 */
        array('nickname', 'checkNicknameLength', -33, self::EXISTS_VALIDATE, 'callback'), // 昵称长度不合法
        array('nickname', 'checkDenyNickname', -31, self::EXISTS_VALIDATE, 'callback'), // 昵称禁止注册
        array('nickname', 'checkNickname', -32, self::EXISTS_VALIDATE, 'callback'),
        array('nickname', '', -30, self::EXISTS_VALIDATE, 'unique'), // 昵称被占用
    );

    protected $_auto = array(
        array('password', 'autoPassworkMd5', self::MODEL_BOTH, 'callback'),
        array('reg_time', NOW_TIME, self::MODEL_INSERT),
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('update_time', NOW_TIME),
        array('status', 'getStatus', self::MODEL_BOTH, 'callback'),
    );

    /**
     * 检测用户名长度
     * @param  string $username 用户名
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkUsernameLength($username)
    {
        $length = mb_strlen($username, 'utf-8'); // 当前数据长度
        if ($length < modC('USERNAME_MIN_LENGTH',2,'USERCONFIG') || $length > modC('USERNAME_MAX_LENGTH',32,'USERCONFIG')) {
            return false;
        }
        return true;
    }

    /**
     * 检测用户名是不是被禁止注册(保留用户名)
     * @param  string $username 用户名
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyUsername($username)
    {
        $denyName = M("Config")->where(array('name' => 'USER_NAME_BAOLIU'))->getField('value');
        if ($denyName!='') {
            $denyName=explode(',',$denyName);
            foreach ($denyName as $val) {
                if (!is_bool(strpos($username,$val))) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 检测用户名是否合格
     * @param  string $username 用户名
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkUsername($username)
    {
        // 如果用户名中有空格，不允许注册
        if (strpos($username, ' ') !== false) {
            return false;
        }
        preg_match("/^[a-zA-Z0-9_]{0,64}$/", $username, $result);

        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * 检测邮箱是不是被禁止注册
     * @param  string $email 邮箱
     * @return boolean       ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyEmail($email)
    {
        return true; // TODO: 暂不限制，下一个版本完善
    }

    /**
     * 检测手机是不是被禁止注册
     * @param  string $mobile 手机
     * @return boolean        ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMobile($mobile)
    {
        return true; // TODO: 暂不限制，下一个版本完善
    }

    /**
     * 检测昵称长度
     * @param  string $nickname 昵称
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkNicknameLength($nickname)
    {
        $length = mb_strlen($nickname, 'utf-8'); // 当前数据长度
        if ($length < modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG') || $length > modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG')) {
            return false;
        }
        return true;
    }

    /**
     * 检测用户名是否被禁止注册
     * @param  string $nickname 昵称
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyNickname($nickname)
    {
        $denyName = M("Config")->where(array('name' => 'USER_NAME_BAOLIU'))->getField('value');
        if ($denyName != '') {
            $denyName = explode(',', $denyName);
            foreach ($denyName as $val) {
                if (!is_bool(strpos($nickname, $val))) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 检测检测昵称是否合格
     * @param  string $nickname 昵称
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkNickname($nickname)
    {
        //如果用户名中有空格，不允许注册
        if (strpos($nickname, ' ') !== false) {
            return false;
        }
        preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $nickname, $result);

        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * 自动完成密码 MD5 加密
     * @param  string $password 需要加密的密码
     */
    protected function autoPassworkMd5($password)
    {
        return get_password_md5($password,C('DATA_AUTH_KEY'));
    }

    /**
     * 根据配置指定用户状态
     * @return integer 用户状态
     */
    protected function getStatus()
    {
        return true; // TODO: 暂不限制，下一个版本完善
    }

    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type 用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1)
    {
        $map = array();
        switch ($type) {
            case 1:
                $map['username'] = $username;
                break;
            case 2:
                $map['email'] = $username;
                break;
            case 3:
                $map['mobile'] = $username;
                break;
            case 4:
                $map['id'] = $username;
                break;
            default:
                return 0; // 参数错误
        }
        /* 获取用户数据 */
        $user = $this->where($map)->find();

        // 验证密码输入次数
        $return = check_action_limit('input_password','users',$user['id'],$user['id']);
        if($return && !$return['state']){
            return $return['info'];
        }

        if (is_array($user) && $user['status']) {
            /* 验证用户密码 */
            if (get_password_md5($password, C('DATA_AUTH_KEY')) === $user['password']) {
                $this->updateLogin($user['id']); // 更新用户登录信息

                return $user; // 登录成功，返回用户数据
            } else {
                action_log('input_password','users',$user['id'],$user['id']);
                return -2; // 密码错误
            }
        } else {
            return -1; // 用户不存在或被禁用
        }
    }

    /**
     * 更新用户登录信息
     * @param  integer $uid 用户ID
     */
    protected function updateLogin($uid)
    {
        $data = array(
            'id' => $uid,
            'login' => array('exp', '`login`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip' => get_client_ip(1),
        );
        $this->save($data);

        //记录行为
        action_log('user_login', 'users', $uid, $uid);
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout()
    {
        session('_AUTH_LIST_' . get_uid() . '1', null);
        session('_AUTH_LIST_' . get_uid() . '2', null);
        session('user_auth', null);
        session('user_auth_sign', null);

        cookie('OX_LOGGED_USER', NULL);
    }

    /**
     * 根据 ID 获取昵称
     * @param  integer $uid 用户 ID
     */
    public function getNickname($uid)
    {
        $nickname = $this->where('id='.$uid)->getfield('nickname');

        return $nickname;
    }

    /**
     * 更新用户信息
     * @param int    $uid      用户 ID
     * @param string $password 密码，用来验证
     * @param array  $data     修改的字段数组
     * @return true            修改成功，false 修改失败
     */
    public function updateUserFields($uid, $password, $data)
    {
        if (empty($uid) || empty($password) || empty($data)) {
            $this->error = '参数错误！25';
            return false;
        }

        //更新前检查用户密码
        if (!$this->verifyUser($uid, $password)) {
            $this->error = '验证出错：密码不正确！';
            return false;
        }

        //更新用户信息
        $data = $this->create($data, 2); //指定此处为更新数据
        if ($data) {
            return $this->where(array('id' => $uid))->save($data);
        }
        return false;
    }

    /**
     * 验证用户密码
     * @param int     $uid         用户id
     * @param string  $password_in 密码
     * @return boolean             true 验证成功，false 验证失败
     */
    public function verifyUser($uid, $password_in)
    {
        $password = $this->getFieldById($uid, 'password');
        if (get_password_md5($password_in, C('DATA_AUTH_KEY')) === $password) {
            return true;
        }
        return false;
    }
}
