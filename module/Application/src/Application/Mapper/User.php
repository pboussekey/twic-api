<?php
/**
 * TheStudnet (http://thestudnet.com)
 *
 * User
 */
namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Application\Model\Role as ModelRole;

/**
 * Class  User
 */
class User extends AbstractMapper
{



    function getFollowSelects($user_id){

            $followers_select = new Select('contact');
            $followers_select->columns([
                'user_id' => 'contact_id',
                'count' => new Expression('SUM(IF(contact.user_id IS NOT NULL, 1, 0))')
            ])
            ->join('user', 'user.id = contact.user_id', [])
            ->where('contact.deleted_date IS NULL')
            ->where('user.deleted_date IS NULL')
            ->where(['contact.contact_id' => $user_id])
            ->group('contact.contact_id');

            $followings_select = new Select('contact');
            $followings_select->columns([
                'user_id' => 'user_id',
                'count' => new Expression('SUM(IF(contact.contact_id IS NOT NULL, 1, 0))')
            ])
            ->join('user', 'user.id = contact.contact_id', [])
            ->where('contact.deleted_date IS NULL')
            ->where('user.deleted_date IS NULL')
            ->where(['contact.user_id' => $user_id])
            ->group('contact.user_id');

            $connections_select = new Select('contact');
            $connections_select->columns([
                'user_id' => 'user_id',
                'count' => new Expression('SUM(IF(contact.contact_id IS NOT NULL, 1, 0))')
            ])
            ->join(['connection' => 'contact'], 'contact.contact_id = connection.user_id AND contact.user_id = connection.contact_id', [])
            ->join('user', 'user.id = contact.contact_id', [])
            ->where('contact.deleted_date IS NULL')
            ->where('connection.deleted_date IS NULL')
            ->where('user.deleted_date IS NULL')
            ->where(['contact.user_id' => $user_id])
            ->group('contact.user_id');
            return [
                'followers' => $followers_select,
                'followings' => $followings_select,
                'connections' => $connections_select,
            ];

    }

    public function get($user_id, $me, $is_admin = false)
    {

        $follow_selects = $this->getFollowSelects($user_id);
        $select_communs_user = $this->tableGateway->getSql()->select();
        $select_communs_user->columns(['user$nbr_user_common' => new Expression('COUNT(true)')])
            ->join('contact', new Expression('contact.user_id=user.id AND contact.accepted_date IS NOT NULL AND contact.deleted_date IS NULL'), [])
            ->join(['ct' => 'contact'], new Expression('contact.contact_id=ct.contact_id AND ct.accepted_date IS NOT NULL AND ct.deleted_date IS NULL'), [])
            ->where(['user.id' => $me])
            ->where(['user.id=`user$id`']);



        $columns = [
            'user$id' => new Expression('user.id'),
            'firstname',
            'gender',
            'lastname',
            'nickname',
            'email',
            'background',
            'has_social_notifier',
            'has_academic_notifier',
            'user$birth_date' => new Expression('DATE_FORMAT(user.birth_date, "%Y-%m-%dT%TZ")'),
            'position',
            'interest',
            'avatar',
            'organization_id',
            'ambassador',
            'email_sent',
            'graduation_year',
            'linkedin_url',
            'user$created_date' => new Expression('DATE_FORMAT(user.created_date, "%Y-%m-%dT%TZ")'),
            'user$invitation_date' => new Expression('DATE_FORMAT(user.invitation_date, "%Y-%m-%dT%TZ")'),
            'user$contact_state' => $this->getSelectContactState($me),
            'user$nbr_user_common' => $select_communs_user,
            'user$welcome_date' =>  new Expression('DATE_FORMAT(DATE_ADD(user.welcome_date, INTERVAL user.welcome_delay DAY), "%Y-%m-%dT%TZ")')
        ];

        if($user_id === $me || (is_array($user_id) && count($user_id) === 1 && in_array($me, $user_id))){
            $columns[] = 'swap_email';
        }

        $select = $this->tableGateway->getSql()->select();
        $select->columns($columns)
            ->join(['followers' => $follow_selects['followers']], 'followers.user_id = user.id', ['user$followers_count' => 'count'], $select::JOIN_LEFT)
            ->join(['followings' => $follow_selects['followings']], 'followings.user_id = user.id', ['user$followings_count' => 'count'], $select::JOIN_LEFT)
            ->join(['connections' => $follow_selects['connections']], 'connections.user_id = user.id', ['user$contacts_count' => 'count'], $select::JOIN_LEFT)
            ->join(['nationality' => 'country'], 'nationality.id=user.nationality', ['nationality!id' => 'id', 'short_name'], $select::JOIN_LEFT)
            ->join(['origin' => 'country'], 'origin.id=user.origin', ['origin!id' => 'id', 'short_name'], $select::JOIN_LEFT)
            ->join(['user_address' => 'address'], 'user.address_id = user_address.id', ['user_address!id' => 'id','street_no','street_type','street_name','floor','door','apartment','building','longitude','latitude','timezone'], $select::JOIN_LEFT)
            ->join(['user_address_division' => 'division'], 'user_address_division.id=user_address.division_id', ['user_address_division!id' => 'id','name'], $select::JOIN_LEFT)
            ->join(['user_address_city' => 'city'], 'user_address_city.id=user_address.city_id', ['school_address_city!id' => 'id','name'], $select::JOIN_LEFT)
            ->join(['user_address_country' => 'country'], 'user_address_country.id=user_address.country_id', ['user_address_country!id' => 'id','short_name','name'], $select::JOIN_LEFT)
            ->where(['user.id' => $user_id])
            ->quantifier('DISTINCT');

            //@TODO Role
        if ($is_admin === false && $user_id !== $me) {
            $select->join('user_role', 'user_role.user_id=user.id', []);
            $select->join(['co' => 'circle_organization'], 'co.organization_id=user.organization_id', []);
            $select->join('circle_organization', 'circle_organization.circle_id=co.circle_id', []);
            $select->join(['circle_organization_user' => 'user'], 'circle_organization_user.organization_id=circle_organization.organization_id', []);
            $select->where([' ( circle_organization_user.id = ? OR user_role.role_id = '.ModelRole::ROLE_ADMIN_ID . ') ' => $me]);
        }
        return $this->selectWith($select);
    }

    public function getAffinitySelect($user_id){

        // si il est dans la meme org que toi 4 points et meme année 40 si c un cours 8 points sinon les autres 2 points
        $page_affinity = new Select(['user_pages' => 'page_user']);
        $page_affinity->columns([
            'user_id' => new Expression('other_user.id'),
            'affinity' => new Expression('SUM(CASE page.type  WHEN "organization" THEN 20 * IF(other_user.graduation_year = user.graduation_year, 2, 1) WHEN "course" THEN 20 ELSE 10 END)')
        ])
            ->join('page', 'user_pages.page_id = page.id',[])
            ->join('user', 'user_pages.user_id = user.id',[])
            ->join(['page_users' => 'page_user'],'user_pages.page_id = page_users.page_id', [])
            ->join(['other_user' => 'user'],'page_users.user_id = other_user.id', [])
            ->where(['user_pages.user_id = ?' => $user_id])
            ->where(['other_user.id <> ?' => $user_id])
            ->group('other_user.id');

        // nombre de tag en commun X 10
        $tag_affinity = new Select(['user_tags' => 'user_tag']);
        $tag_affinity->columns([
            'user_id' => new Expression('other_user.user_id'),
            'affinity' => new Expression('COUNT(DISTINCT other_user.tag_id) * 10')
        ])
            ->join(['other_user' => 'user_tag'],'user_tags.tag_id = other_user.tag_id', [])
            ->where(['user_tags.user_id = ?' => $user_id])
            ->where(['other_user.user_id <> ?' => $user_id])
            ->group('other_user.user_id');

        // 1 par contact en commun
        $contact_affinity = new Select(['user_contacts' => 'contact']);
        $contact_affinity->columns([
            'user_id' => new Expression(' CASE WHEN contact_users.contact_id = user_contacts.user_id THEN contact_users.user_id ELSE contact_users.contact_id END'),
            'affinity' => new Expression('SUM(CASE WHEN user_contacts.user_id = contact_users.contact_id THEN 1000 ELSE 1 END)')
        ])
            ->join(['contact_users' => 'contact'], 'user_contacts.contact_id = contact_users.user_id',[])
            ->where(['user_contacts.user_id = ?' => $user_id])
            ->where('user_contacts.accepted_date IS NOT NULL AND contact_users.accepted_date IS NOT NULL')
            ->where('user_contacts.deleted_date IS NULL AND contact_users.deleted_date IS NULL')
            ->group('contact_users.user_id');

        // 50 points
        $program_affinity = new Select(['ouser' => 'user']);
        $program_affinity->columns([
            'user_id' => new Expression('page_program_user.user_id'),
            'affinity' => new Expression('IF(`page_program_user`.`user_id` IS NOT NULL, IF(user.id IS NOT NULL, 60, 10),0)')
        ])
            ->join(['other_page_program_user' => 'page_program_user'],'other_page_program_user.user_id = ouser.id', [])
            ->join('page_program_user', new Expression('page_program_user.user_id = ?', $user_id), [])
            ->join('user', 'user.id = page_program_user.user_id AND user.graduation_year = ouser.graduation_year', [], $program_affinity::JOIN_LEFT)
            ->where(['other_page_program_user.user_id <> page_program_user.user_id'])
            ->where(['other_page_program_user.page_program_id = page_program_user.page_program_id']);

        $select = $this->tableGateway->getSql()->select();
        $select->columns(['user_id' => 'id', 'affinity' => new Expression(
            "COALESCE(program_affinity.affinity,0) + COALESCE(page_affinity.affinity,0) + COALESCE(contact_affinity.affinity,0) + COALESCE(tag_affinity.affinity,0)")])
               ->join(['page_affinity' => $page_affinity], 'page_affinity.user_id = user.id', [], $select::JOIN_LEFT)
               ->join(['contact_affinity' => $contact_affinity], 'contact_affinity.user_id = user.id', [], $select::JOIN_LEFT)
               ->join(['tag_affinity' => $tag_affinity], 'tag_affinity.user_id = user.id', [], $select::JOIN_LEFT)
               ->join(['program_affinity' => $program_affinity], 'program_affinity.user_id = user.id', [], $select::JOIN_LEFT);

        return $select;

   }

    public function getList(
        $user_id,
        $is_admin,
        $post_id = null,
        $search = null,
        $page_id = null,
        $order = null,
        array $exclude = null,
        $contact_state = null,
        $unsent = false,
        $role = null,
        $conversation_id = null,
        $page_type = null,
        $email = null,
        $is_pinned = null,
        $state = null,
        $is_active = null,
        $shared_id = null,
        $alumni = null,
        $tags = null,
        $view_id = null
    ) {
        $follow_selects = $this->getFollowSelects($user_id);

        $select = $this->tableGateway->getSql()->select();

        if ($is_admin) {
            $columns =  [
                'user$id' => new Expression('user.id'),
                'firstname', 'lastname', 'email', 'nickname', 'ambassador', 'email_sent', 'initial_email',
                'user$birth_date' => new Expression('DATE_FORMAT(user.birth_date, "%Y-%m-%dT%TZ")'),
                'position', 'interest', 'avatar', 'suspension_date', 'suspension_reason', 'graduation_year',
                'user$contact_state' => $this->getSelectContactState($user_id)
                ];
        } else {
             $columns =
                [
                  'user$id' => new Expression('user.id'),
                  'firstname', 'lastname', 'email', 'nickname','ambassador', 'initial_email',
                  'user$birth_date' => new Expression('DATE_FORMAT(user.birth_date, "%Y-%m-%dT%TZ")'),
                  'position', 'interest', 'avatar', 'graduation_year',
                  'user$contact_state' => $this->getSelectContactState($user_id)
                ];

            $select
                  ->join(['followers' => $follow_selects['followers']], 'followers.user_id = user.id', ['user$followers_count' => 'count'], $select::JOIN_LEFT)
                  ->join(['followings' => $follow_selects['followings']], 'followings.user_id = user.id', ['user$followings_count' => 'count'], $select::JOIN_LEFT)
                  ->join(['connections' => $follow_selects['connections']], 'connections.user_id = user.id', ['user$contacts_count' => 'count'], $select::JOIN_LEFT)
                  ->join(['co' => 'circle_organization'], 'co.organization_id=user.organization_id', [])
                  ->join('circle_organization', 'circle_organization.circle_id=co.circle_id', [])
                  ->join(['circle_organization_user' => 'user'], 'circle_organization_user.organization_id=circle_organization.organization_id', [])
                  ->where(['circle_organization_user.id' => $user_id]);
        }
        if(null !== $email){
            $columns[] = 'initial_email';
        }
        $select->columns($columns);
        $select->where('user.deleted_date IS NULL')
            ->group('user.id')
            ->quantifier('DISTINCT');

        if (null !== $order) {
            switch ($order['type']) {
            case 'name':
                $select->order(new Expression('user.is_active DESC, COALESCE(NULLIF(user.nickname,""),TRIM(CONCAT_WS(" ",user.lastname,user.firstname, user.email)))'));
                break;
            case 'firstname' :
                $select->order('user.firstname ASC');
                break;
            case 'random' :
                $select->order(new Expression('RAND(?)', $order['seed']));
                break;
            case 'affinity' :
                $select->join(['affinity' => $this->getAffinitySelect($user_id)], 'user.id = affinity.user_id', []);
                if(isset($order['seed'])){
                    $select->order([new Expression('user.id = ?', $user_id), new Expression('ROUND(affinity / 50) DESC'), new Expression('RAND(?)', $order['seed'])]);
                }
                else{
                    $select->order([new Expression('user.id = ?', $user_id), 'affinity DESC']);

                }
                    //->order(new Expression(' ( ROUND( affinity.affinity ) * 10 / ( MAX(affinity.affinity) OVER() ) ) DESC '));
                break;
            default:
                $select->order(['user.id' => 'DESC']);
            }
        } else {
            $select->order(['user.id' => 'DESC']);
        }

        if ($exclude) {
            $select->where->notIn('user.id', $exclude);
        }
        if (null !== $post_id) {
            $select->join('post_like', 'post_like.user_id=user.id', [])
                ->where(['post_like.post_id' => $post_id])
                ->where(['post_like.is_like IS TRUE']);
        }
        if (null !== $shared_id) {
            $select->join('post', 'post.user_id=user.id', [])
                ->where(['post.shared_id' => $shared_id])
                ->where(['post.deleted_date IS NULL']);
        }
        if (null !== $view_id) {
            $select->join('post_user', 'post_user.user_id=user.id', [])
                ->where(['post_user.post_id' => $view_id])
                ->where(['post_user.view_date IS NOT NULL']);
        }
        if (!empty($conversation_id)) {
            $select->join('conversation_user', 'conversation_user.user_id=user.id', [])
                ->where(['conversation_user.conversation_id' => $conversation_id]);
        }

        if (null !== $contact_state) {
            if (!is_array($contact_state)) {
                $contact_state = [$contact_state];
            }
            $select->having(['user$contact_state' => $contact_state]);
            if (in_array(0, $contact_state)) {
                $select->having('user$contact_state IS NULL', Predicate::OP_OR);
            }
        }
        if (!empty($state) || !empty($role) || !empty($page_type) || !empty($page_id) || null !== $is_pinned) {
            $select->join(['pu' => 'page_user'], 'pu.user_id=user.id', [])
                ->join(['p' => 'page'], 'pu.page_id=p.id', []);
        }


        if (!empty($page_id)) {
            $select->where(['pu.page_id' => $page_id]);
        }
        if (!empty($role)) {
            $select->where(['pu.role' => $role]);
        }
        if (!empty($state)) {
            $select->where(['pu.state' => $state]);
        }
        if(null !== $is_pinned){
            $select->where(['is_pinned' => $is_pinned]);
        }
        if(!empty($page_type)) {
            $select->where(['p.type' => $page_type]);
        }
        if ($unsent === true) {
            $select->where(['user.email_sent IS FALSE']);
        }
        else if ($unsent === false){
            $select->where(['user.email_sent IS TRUE']);
        }
        if(!empty($email)) {
            $select->where->NEST
                ->in(new Expression('LOWER(user.email)'),$email)
                ->OR
                ->in(new Expression('LOWER(user.initial_email)'),$email)
            ->UNNEST;
        }
        if ($is_active === true) {
            $select->where(['user.is_active IS TRUE']);
        }
        else if ($is_active === false){
            $select->where(['user.is_active IS FALSE']);
        }
        if($alumni === true){
            $select->where(['user.graduation_year < YEAR(CURDATE())']);
        }
        else if($alumni === false){
            $select->where(['(user.graduation_year = YEAR(CURDATE()) OR user.graduation_year IS NULL)']);
        }


        if(!empty($tags)) {
            $s = $this->tableGateway->getSql()->select();
            $s->columns(['id'])
              ->join(['t1' => $select], new Expression('`t1`.`user$id`=`user`.`id`'), []);
            $select = $s;

            $select->join('user_tag', 'user_tag.user_id = user.id', [], $select::JOIN_LEFT)
                ->join('tag', 'user_tag.tag_id = tag.id', [], $select::JOIN_LEFT)
                ->where->in(new Expression('CONCAT_WS(":", user_tag.category, tag.name)'),$tags);

            $select->group('user.id')->having(['COUNT(`user`.`id`) = ?' => count($tags)]);
        }

        if (!empty($search)) {
          $tags_break = explode(' ', trim(preg_replace('/\s+/',' ',preg_replace('/([A-Z][a-z0-9])/',' ${0}', $search))));
          $nbt = count($tags_break);
          $last = end($tags_break);

          if($nbt > 1) {
              array_pop($tags_break);
              // le search dernier mot
              $s = $this->tableGateway->getSql()->select();
              $s->columns(['id'])
                ->join(['t2' => $select], new Expression('`t2`.`user$id`=`user`.`id`'), []);
              $select = $s;

              $select->join('user_tag', 'user_tag.user_id = user.id', [], $select::JOIN_LEFT)
                  ->join('tag', 'user_tag.tag_id = tag.id', [], $select::JOIN_LEFT)
                  ->join('tag_breakdown', 'tag_breakdown.tag_id = tag.id', [], $select::JOIN_LEFT)
                  ->where(['tag_breakdown.tag_part' => $tags_break])
                  ->group('user.id')->having(['COUNT(`user`.`id`) = ?' => $nbt-1]);
          }

          $s = $this->tableGateway->getSql()->select();
          $s->columns(['id'])
            ->join(['t3' => $select], new Expression('`t3`.`user$id`=`user`.`id`'), []);
          $select = $s;

          $select->join('user_tag', 'user_tag.user_id = user.id', [], $select::JOIN_LEFT)
              ->join('tag', 'user_tag.tag_id = tag.id', [], $select::JOIN_LEFT)
              ->join('tag_breakdown', 'tag_breakdown.tag_id = tag.id', [], $select::JOIN_LEFT)
              ->where(['tag_breakdown.tag_part LIKE ?'   => $last.'%']);
              $select->group('user.id');
        }


        return $this->selectWith($select);
    }

    public function getEmailUnique($email, $user = null, $is_active = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('user$nb_user' => new Expression('COUNT(true)')))
            ->where(array('( user.email = ? ' =>  $email))
            ->where(array(' user.initial_email = ? ) ' =>  $email), Predicate::OP_OR)
            ->where(array('user.deleted_date IS NULL'));

        if (null !== $user) {
            $select->where(array('user.id <> ?' => $user));
        }

        if (true === $is_active) {
            $select->where(['user.is_active IS TRUE']);
        } else if(false === $is_active) {
            $select->where(['user.is_active IS FALSE']);
        }

        return $this->selectWith($select);
    }

    public function getNbrSisUnique($sis)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('user$nb_user' => new Expression('COUNT(true)')))
            ->where(array('user.sis' => $sis))
            ->where(array('user.deleted_date IS NULL'))
            ->where(array('user.sis IS NOT NULL'))
            ->where(array('user.sis <> ""'));

        return $this->selectWith($select);
    }

      /**
       * Check if an account token is valid
       *
       * @param string $token
       * @param string $email
       *
       * @return \Zend\Db\Sql\Select
       */
    public function checkUser($token = null, $email = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(
          ['firstname',
           'lastname',
           'avatar',
           'nickname',
           'is_active',
           'user$email' => new Expression('IF(user.email = "'.$email.'", user.email , CONCAT(SUBSTRING(user.email, 1, 4), "******", SUBSTRING(user.email, -4)))'),
           'user$initial_email' => new Expression('IF(user.initial_email IS NOT NULL AND user.initial_email = "'.$email.'", NULL, user.initial_email)'),
           'user$invitation_date' => new Expression('DATE_FORMAT(user.invitation_date, "%Y-%m-%dT%TZ")'),
           'user$linkedin_id' => new Expression("IF(linkedin_id IS NOT NULL, true, false)"),
           'organization_id']);
        if(null !== $token){
            $select
            ->join('preregistration', 'preregistration.user_id = user.id', [
                'email',
                'firstname',
                'lastname',
                'organization_id',
                'account_token',
                'user_id'
            ], $select::JOIN_RIGHT)
           ->where(['preregistration.account_token' => $token]);

        }
        if(null !== $email){
            $select->where(['( user.email = ? ' => $email])
                   ->where([' user.initial_email = ? )' => $email], Predicate::OP_OR);
        }
        $select->join('page', 'user.organization_id = page.id', ['id', 'libelle', 'logo', 'title'], $select::JOIN_LEFT)
               ->where('user.deleted_date IS NULL');
        return $this->selectWith($select);
    }

    /**
     * Get Select Objet for Contact State
     *
     * @param  int $user
     * @return \Zend\Db\Sql\Select
     */
    public function getSelectContactState($user)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(
            ['user$contact_state' => new Expression('IF(followers.id IS NOT NULL, 2, 0) + IF(followings.id IS NOT NULL, 1, 0)')]
        )
            ->join(['followers' => 'contact'], new Expression('followers.contact_id = ? AND followers.user_id = user.id AND followers.deleted_date IS NULL', $user), [], $select::JOIN_LEFT)
            ->join(['followings' => 'contact'], new Expression('followings.user_id = ? AND followings.contact_id = user.id AND followings.deleted_date IS NULL', $user), [], $select::JOIN_LEFT)
            ->where(['user.id=`user$id`']);

        return $select;
    }



    /**
     * Get Select Objet for Contact Count
     *
     * @return \Zend\Db\Sql\Select
     */
    public function getSelectContactCount($user_id)
    {
          $select = $this->tableGateway->getSql()->select();
          $select->columns([
              'user_id' => 'id',
              'contacts_count' => new Expression('COUNT( DISTINCT connection.id )'),
              'followings_count' => new Expression('COUNT( DISTINCT followings.id )'),
              'followers_count' => new Expression('COUNT( DISTINCT followers.id )')
            ])
            ->join(['followers' => 'contact'], new Expression('followers.contact_id = user.id AND followers.deleted_date IS NULL'), [], $select::JOIN_LEFT)
            ->join(['follower_user' => 'user'], new Expression('followers.user_id = follower_user.id AND follower_user.deleted_date IS NULL'), [], $select::JOIN_LEFT)
            ->join(['connection' => 'contact'], new Expression('connection.user_id = user.id AND connection.contact_id = follower_user.id'), [], $select::JOIN_LEFT)
            ->join(['followings' => 'contact'], new Expression('followings.user_id = user.id AND followings.deleted_date IS NULL'), [], $select::JOIN_LEFT)
            ->join(['following_user' => 'user'], new Expression('followings.user_id = following_user.id AND following_user.deleted_date IS NULL'), [], $select::JOIN_LEFT)
            ->where(['user.id' => $user_id]);
          return $select;
    }

    /**
     * Get settings
     *
     * @return \Zend\Db\Sql\Select
     */
    public function getSettings($key)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['has_social_notifier', 'has_academic_notifier'])
            ->join('event_user', 'event_user.user_id = user.id', [])
            ->join('event', new Expression('event.date >=  DATE_SUB(NOW(), INTERVAL 14 DAY) AND event_user.event_id = event.id AND MD5(CONCAT(user.id, event.id,  DATE_FORMAT(event.date, "%M %D"), event.object)) = ?', $key), []);
        return $this->selectWith($select);
    }


    public function updateSettings($key, $has_social_notifier, $has_academic_notifier)
    {
        $update = $this->tableGateway->getSql()->update();
        $update->set(['has_social_notifier' => $has_social_notifier, 'has_academic_notifier' => $has_academic_notifier])
        ->join('event_user', 'event_user.user_id = user.id', [])
        ->join('event', new Expression('event.date >=  DATE_SUB(NOW(), INTERVAL 14 DAY) AND event_user.event_id = event.id AND MD5(CONCAT(user.id, event.id,  DATE_FORMAT(event.date, "%M %D"), event.object)) = ?', $key), []);

        return $this->updateWith($update);
    }
}
