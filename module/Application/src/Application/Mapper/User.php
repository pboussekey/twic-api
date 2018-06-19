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
    public function get($user_id, $me, $is_admin = false)
    {
        $columns = [
            'user$id' => new Expression('user.id'),
            'firstname',
            'gender',
            'lastname',
            'nickname',
            'email',
            'background',
            'has_email_notifier',
            'user$birth_date' => new Expression('DATE_FORMAT(user.birth_date, "%Y-%m-%dT%TZ")'),
            'position',
            'interest',
            'avatar',
            'organization_id',
            'ambassador',
            'email_sent',
            'user$created_date' => new Expression('DATE_FORMAT(user.created_date, "%Y-%m-%dT%TZ")'),
            'user$invitation_date' => new Expression('DATE_FORMAT(user.invitation_date, "%Y-%m-%dT%TZ")'),
            'user$contacts_count' => $this->getSelectContactCount(),
            'user$contact_state' => $this->getSelectContactState($me),
            'user$welcome_date' =>  new Expression('DATE_FORMAT(DATE_ADD(user.welcome_date, INTERVAL user.welcome_delay DAY), "%Y-%m-%dT%TZ")')
        ];

        if($user_id === $me || (is_array($user_id) && count($user_id) === 1 && in_array($me, $user_id))){
            $columns[] = 'swap_email';
        }


        $select = $this->tableGateway->getSql()->select();
        $select->columns($columns)
            ->join(array('nationality' => 'country'), 'nationality.id=user.nationality', ['nationality!id' => 'id', 'short_name'], $select::JOIN_LEFT)
            ->join(array('origin' => 'country'), 'origin.id=user.origin', ['origin!id' => 'id', 'short_name'], $select::JOIN_LEFT)
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

        $page_affinity = new Select(['user_pages' => 'page_user']);
        $page_affinity->columns([
            'user_id' => new Expression('other_user.id'),
            'affinity' => new Expression('SUM(CASE page.type  WHEN "organization" THEN 4 WHEN "course" THEN 8 ELSE 2 END)')
        ])
        ->join('page', 'user_pages.page_id = page.id',[])
        ->join(['page_users' => 'page_user'],'user_pages.page_id = page_users.page_id', [])
        ->join(['other_user' => 'user'],'page_users.user_id = other_user.id', [])
        ->where(['user_pages.user_id = ?' => $user_id])
        ->where(['other_user.id <> ?' => $user_id])
        ->group('other_user.id');

        $tag_affinity = new Select(['user_tags' => 'user_tag']);
        $tag_affinity->columns([
            'user_id' => new Expression('other_user.user_id'),
            'affinity' => new Expression('COUNT(DISTINCT other_user.tag_id) * 10')
        ])
        ->join(['other_user' => 'user_tag'],'user_tags.tag_id = other_user.tag_id', [])
        ->where(['user_tags.user_id = ?' => $user_id])
        ->where(['other_user.user_id <> ?' => $user_id])
        ->group('other_user.user_id');


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

        $select = $this->tableGateway->getSql()->select();
        $select->columns(['user_id' => 'id', 'affinity' => new Expression("COALESCE(page_affinity.affinity,0) + COALESCE(contact_affinity.affinity,0) + COALESCE(tag_affinity.affinity,0)")])
               ->join(['page_affinity' => $page_affinity], 'page_affinity.user_id = user.id', [], $select::JOIN_LEFT)
               ->join(['contact_affinity' => $contact_affinity], 'contact_affinity.user_id = user.id', [], $select::JOIN_LEFT)
               ->join(['tag_affinity' => $tag_affinity], 'tag_affinity.user_id = user.id', [], $select::JOIN_LEFT);

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
        $state = null
    ) {
        $select = $this->tableGateway->getSql()->select();

        if ($is_admin) {
            $columns =  [
                'user$id' => new Expression('user.id'),
                'firstname', 'lastname', 'email', 'nickname', 'ambassador', 'email_sent', 'initial_email',
                'user$birth_date' => new Expression('DATE_FORMAT(user.birth_date, "%Y-%m-%dT%TZ")'),
                'position', 'interest', 'avatar', 'suspension_date', 'suspension_reason',
                'user$contact_state' => $this->getSelectContactState($user_id),
                'user$contacts_count' => $this->getSelectContactCount()
                ];
        } else {
             $columns =
                [
                'user$id' => new Expression('user.id'),
                'firstname', 'lastname', 'email', 'nickname','ambassador', 'initial_email',
                'user$birth_date' => new Expression('DATE_FORMAT(user.birth_date, "%Y-%m-%dT%TZ")'),
                'position', 'interest', 'avatar',
                'user$contact_state' => $this->getSelectContactState($user_id),
                'user$contacts_count' => $this->getSelectContactCount()
                ];

            $select->join(['co' => 'circle_organization'], 'co.organization_id=user.organization_id', []);
            $select->join('circle_organization', 'circle_organization.circle_id=co.circle_id', []);
            $select->join(['circle_organization_user' => 'user'], 'circle_organization_user.organization_id=circle_organization.organization_id', []);
            $select->where(['circle_organization_user.id' => $user_id]);
        }
        if(null !== $email){
            $columns[] = 'initial_email';
        }
        $select->columns(
           $columns
        );
        $select->where('user.deleted_date IS NULL')
            ->group('user.id')
            ->quantifier('DISTINCT');

        if (null !== $order) {
            switch ($order['type']) {
            case 'name':
                $select->order(new Expression('user.is_active DESC, COALESCE(NULLIF(user.nickname,""),TRIM(CONCAT_WS(" ",user.lastname,user.firstname, user.email)))'));
                break;
            case 'firstname':
                $select->order('user.firstname ASC');
                break;
            case 'random':
                $select->order(new Expression('RAND(?)', $order['seed']));
                break;
            case 'affinity':
                $select->join(['affinity' => $this->getAffinitySelect($user_id)], 'user.id = affinity.user_id', [])
                    ->order([new Expression('user.id = ?', $user_id), 'affinity DESC']);
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
        if (!empty($post_id)) {
            $select->join('post_like', 'post_like.user_id=user.id', [])
                ->where(['post_like.post_id' => $post_id])
                ->where(['post_like.is_like IS TRUE']);
        }
        if (!empty($conversation_id)) {
            $select->join('conversation_user', 'conversation_user.user_id=user.id', [])
                ->where(['conversation_user.conversation_id' => $conversation_id]);
        }
        if (null !== $search) {

          $tags = explode(' ', $search);
          $select->join('user_tag', 'user_tag.user_id = user.id', [], $select::JOIN_LEFT)
              ->join('tag', 'user_tag.tag_id = tag.id', [], $select::JOIN_LEFT)
              ->where(['( CONCAT_WS(" ", user.lastname, user.firstname) LIKE ? ' =>  $search . '%'])
              ->where(['CONCAT_WS(" ", user.firstname, user.lastname) LIKE ? ' => $search.'%'], Predicate::OP_OR)
              ->where(['user.email LIKE ? ' => $search.'%'], Predicate::OP_OR)
              ->where(['user.initial_email LIKE ? ' => $search.'%'], Predicate::OP_OR)
              ->where(['tag.name'   => $tags], Predicate::OP_OR)
              ->where(['1)'])
              ->having(['( COUNT(DISTINCT tag.id) = ? OR COUNT(DISTINCT tag.id) = 0 ' => count($tags)])
              ->having([' CONCAT_WS(" ", user.lastname, user.firstname) LIKE ? ' => $search . '%'], Predicate::OP_OR)
              ->having(['CONCAT_WS(" ", user.firstname, user.lastname) LIKE ? ' => $search.'%'], Predicate::OP_OR)
              ->having(['user.email LIKE ? ' => $search.'%'], Predicate::OP_OR)
              ->having(['user.initial_email LIKE ? )' => $search.'%'], Predicate::OP_OR);
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
        return $this->selectWith($select);
    }

    public function getEmailUnique($email, $user)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('user$nb_user' => new Expression('COUNT(true)')))
            ->where(array('( user.email = ? ' =>  $email))
            ->where(array(' user.initial_email = ? ) ' =>  $email), Predicate::OP_OR)
            ->where(array('user.deleted_date IS NULL'));

        if (null !== $user) {
            $select->where(array('user.id <> ?' => $user));
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
        $select->columns(['firstname', 'lastname', 'avatar', 'nickname', 'is_active','email']);
        if(null !== $token){
            $select
            ->join('preregistration', 'preregistration.user_id = user.id', [])
            ->where(['preregistration.account_token' => $token]);
        }
        if(null !== $email){
            $select->where(['user.email' => $email]);
        }

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
            array('user$contact_state' => new Expression(
                'IF(contact.accepted_date IS NOT NULL AND contact.deleted_date IS NULL, 3,
	         IF(contact.request_date IS NOT  NULL AND contact.requested <> 1 AND contact.deleted_date IS NULL, 2,
		     IF(contact.request_date IS NOT  NULL AND contact.requested = 1 AND contact.deleted_date IS NULL, 1,0)))'
            ))
        )
            ->join('contact', 'contact.contact_id = user.id', array())
            ->where(array('user.id=`user$id`'))
            ->where(['contact.user_id' => $user]);

        return $select;
    }



    /**
     * Get Select Objet for Contact Count
     *
     * @return \Zend\Db\Sql\Select
     */
    public function getSelectContactCount()
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('user$contacts_count' => new Expression('COUNT(1)')))
            ->join('contact', 'contact.contact_id = user.id', [])
            ->where(array('contact.user_id = `user$id` AND user.deleted_date IS NULL AND contact.accepted_date IS NOT NULL AND contact.deleted_date IS NULL'));

        return $select;
    }
}
