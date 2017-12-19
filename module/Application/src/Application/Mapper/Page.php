<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Select;

class Page extends AbstractMapper
{

    /**
     * Execute Request Get Custom
     *
     * @param  string $libelle
     * @param  int    $id
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getCustom($libelle, $id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('id','libelle','custom'));

        if (null !== $libelle) {
            $select->where(array('page.libelle' => $libelle));
        }
        if (null !== $id) {
            $select->where(array('page.id' => $id));
        }

        return $this->selectWith($select);
    }

    /**
     * Get State and role of the current user on page
     *
     * @param  int $user
     * @return \Zend\Db\Sql\Select
     */
    public function getPageStatus($user)
    {
        $select = new Select('page_user');
        $select->columns(['state', 'role', 'page_id'])
            ->where(['user_id' => $user]);

        return $select;
    }

    public function getListId(
        $me,
        $parent_id = null,
        $type = null,
        $start_date = null,
        $end_date = null,
        $member_id = null,
        $strict_dates = false,
        $is_admin = false,
        $search = null,
        $tags = null,
        $children_id = null,
        $is_member_admin = null,
        $relation_type = null,
        $exclude = null
    ) {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'title'])
            ->where(['page.deleted_date IS NULL'])
            ->quantifier('DISTINCT');

        if ($exclude) {
            $select->where->notIn('page.id', $exclude);
        }
        if (!empty($parent_id)) {
            $select->join('page_relation', 'page_relation.page_id = page.id', ['parent_id'])
                ->where(['page_relation.parent_id' => $parent_id]);
            if (!empty($relation_type)) {
                $select->where(['page_relation.type' => $relation_type]);
            }
        }
        if (!empty($children_id)) {
            $select->join('page_relation', 'page_relation.parent_id = page.id', ['page_id'])
                ->where(['page_relation.page_id' => $children_id]);
            if (!empty($relation_type)) {
                $select->where(['page_relation.type' => $relation_type]);
            }
        }
        if (null !== $type) {
            $select->where(['page.type' => $type]);
        }
        if (null !== $member_id) {
            $select->join(['member' => 'page_user'], 'member.page_id = page.id', [])
                ->where(['member.user_id' => $member_id])
                ->where(['member.state' => 'member']);
            if ($is_member_admin === true) {
                $select->where(['member.role' => 'admin']);
            }
        }

        if (null !== $search) {
            $tags = explode(' ', $search);
            $select->join('page_tag', 'page_tag.page_id = page.id', [], $select::JOIN_LEFT)
                ->join('tag', 'page_tag.tag_id = tag.id', [], $select::JOIN_LEFT)
                ->where(['( page.title LIKE ? ' => '%' . $search . '%'])
                ->where(['tag.name'   => $tags], Predicate::OP_OR)
                ->where(['1)'])
                ->having(['( COUNT(DISTINCT tag.id) = ? OR COUNT(DISTINCT tag.id) = 0 ' => count($tags)])
                ->having([' page.title LIKE ? ) ' => '%' . $search . '%'], Predicate::OP_OR);
        }
        if (!empty($tags)) {
            /*  $select->join('page_tag', 'page_tag.page_id = page.id')
                  ->join('tag', 'tag.id = page_tag.tag_id')
                  ->where(['tag.name' => $tags])
                  ->having(['COUNT(DISTINCT tag.id) = ?' => count($tags)]);*/
        }
        if (null !== $start_date && null !== $end_date) {
            $select->where(['( page.start_date BETWEEN ? AND ? ' => [$start_date,$end_date]])
                ->where(['page.end_date BETWEEN ? AND ?  ' => [$start_date,$end_date]], Predicate::OP_OR)
                ->where(['( page.start_date < ? AND page.end_date > ? ) ) ' => [$start_date,$end_date]], Predicate::OP_OR);
        } else {
            if (null !== $start_date) {
                $paramValue = $strict_dates ? 'page.start_date >= ?' : 'page.end_date >= ?';
                $select->where([$paramValue => $start_date]);
            }
            if (null !== $end_date) {
                $paramValue = $strict_dates ? 'page.end_date <= ?' : 'page.start_date <= ?';
                $select->where([$paramValue => $end_date]);
            }
        }
        if ($is_admin === false) {
            $select->join('page_user', 'page_user.page_id = page.id', [], $select::JOIN_LEFT)
                ->where(["( page.confidentiality = 0 "])
                ->where([" page_user.user_id = ? )" => $me], Predicate::OP_OR);

            $select->join(['pu' => 'page_user'], new Expression("pu.page_id = page.id AND pu.role = 'admin'"), [])
                ->join(['pu_u' => 'user'], 'pu.user_id=pu_u.id', [])
                ->join(['co' => 'circle_organization'], 'co.organization_id=pu_u.organization_id', [])
                ->join('circle_organization', 'circle_organization.circle_id=co.circle_id', [])
                ->join(['circle_organization_user' => 'user'], 'circle_organization_user.organization_id=circle_organization.organization_id', [])
                ->where(['circle_organization_user.id' => $me]);

            // retourne que les couses publié ou tous le reste
            $select->where(['( page.is_published IS TRUE OR page.type <> "course" OR ( page_user.role = "admin" AND page_user.user_id = ? ) )' => $me]);
        }
        $select->order(['page.start_date' => 'DESC'])
            ->group('page.id');

        return $this->selectWith($select);
    }

    public function get($me, $id = null, $parent_id = null, $type = null, $is_admin = false)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(
            [
                'id',
                'title',
                'logo',
                'background',
                'description',
                'confidentiality',
                'admission',
                'location',
                'type',
                'user_id',
                'owner_id',
                'conversation_id',
                'is_published',
                'website',
                'page$start_date' => new Expression('DATE_FORMAT(page.start_date, "%Y-%m-%dT%TZ")'),
                'page$end_date' => new Expression('DATE_FORMAT(page.end_date, "%Y-%m-%dT%TZ")')
            ]
        )->join(
            ['state' => $this->getPageStatus($me)], 'state.page_id = page.id', [
            'page$state' => 'state',
            'page$role' => 'role'
            ], $select::JOIN_LEFT
        )
            ->join(['p_user' => 'user'], 'p_user.id = page.owner_id', ['id', 'firstname', 'lastname', 'avatar', 'ambassador'], $select::JOIN_LEFT)
            ->join(['page_address' => 'address'], 'page.address_id = page_address.id', ['page_address!id' => 'id','street_no','street_type','street_name','floor','door','apartment','building','longitude','latitude','timezone'], $select::JOIN_LEFT)
            ->join(['page_address_division' => 'division'], 'page_address_division.id=page_address.division_id', ['page_address_division!id' => 'id','name'], $select::JOIN_LEFT)
            ->join(['page_address_city' => 'city'], 'page_address_city.id=page_address.city_id', ['school_address_city!id' => 'id','name'], $select::JOIN_LEFT)
            ->join(['page_address_country' => 'country'], 'page_address_country.id=page_address.country_id', ['page_address_country!id' => 'id','short_name','name'], $select::JOIN_LEFT)
            ->where(['page.deleted_date IS NULL']);

        if (null !== $id) {
            $select->where(['page.id' => $id]);
        }
        if (null !== $type) {
            $select->where(['page.type' => $type]);
        }
        if ($is_admin === false) {
            $select->join('page_user', 'page_user.page_id = page.id', [], $select::JOIN_LEFT)
                ->where(["( page.confidentiality = 0 "])
                ->where([" page_user.user_id = ? )" => $me], Predicate::OP_OR);

            $select->join(['pu' => 'page_user'], new Expression("pu.page_id = page.id AND pu.role = 'admin'"), []);
            $select->join(['pu_u' => 'user'], 'pu.user_id=pu_u.id', []);
            $select->join(['co' => 'circle_organization'], 'co.organization_id=pu_u.organization_id', []);
            $select->join('circle_organization', 'circle_organization.circle_id=co.circle_id', []);
            $select->join(['circle_organization_user' => 'user'], 'circle_organization_user.organization_id=circle_organization.organization_id', []);
            $select->where(['circle_organization_user.id' => $me]);
        }
        $select->group('page.id');
        return $this->selectWith($select);
    }

    public function getIdByItem($item_id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id'])
            ->join('item', 'page.id=item.page_id', [])
            ->where(['item.id' => $item_id]);

        return $this->selectWith($select);
    }


    /**
     * Execute Request Get
     *
     * @param int $id
     */
    public function getGradesSelect($id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['average' => new Expression('ROUND(SUM(item_user.rate) / SUM(item.points) * 100)')])
            ->join('item', 'page.id = item.page_id', ['page_id'])
            ->join('item_user', 'item.id = item_user.item_id', ['user_id'])
            ->join('user', 'item_user.user_id = user.id', [])
            ->where(['item.is_grade_published' => 1])
            ->where('item_user.rate IS NOT NULL')
            ->where(['user.organization_id' => $id])
            ->group(['item.page_id', 'item_user.user_id'])
            ->order([new Expression('ROUND(SUM(item_user.rate) / SUM(item.points) * 100) DESC')]);
        
        return $select;
    }

    public function getRankSelect($id, $avg)
    {
        $rank_select->columns(['rank' => new Expression('COUNT(*) + 1')])
            ->from(['g' => $this->getGradesSelect($id)])
            ->group('item_user$user_id')
            ->where(['g.average < ?' => $avg]);
        return $rank_select;
    }

    /**
     * Execute Request Get organization median
     *
     * @param int $id
     */
    public function getMedian($id)
    {
        $grades_select =  $this->getGradesSelect($id);
        $sub_select = new Select();
        $sub_select->columns(['average'])
            ->from(['g' => $grades_select])
            ->join(['r' => new Expression('(SELECT @rownum:=0)')], new Expression('1'), ['row_number' => new Expression('@rownum:=@rownum+1')]);


        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'page$median' => new Expression('AVG(t1.average)')])
            ->join(['t1' => $sub_select], new Expression('1'), [])
            ->where(['page.id' => $id])
            ->where(new Expression('(row_number = FLOOR((@rownum+1)/2)'))
            ->where(new Expression('row_number = FLOOR((@rownum+2)/2))'), Predicate::OP_OR);

        return $this->selectWith($select);
    }

    /**
     * Execute Request Get organization average
     *
     * @param int $id
     */
    public function getAverage($id)
    {
        $grades_select =  $this->getGradesSelect($id);
        $sub_select = new Select();
        $sub_select->columns(['average' => new Expression('AVG(average)')])
            ->from(['g' => $grades_select])
            ->group('item_user$user_id');

        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'page$average' => new Expression('ROUND(AVG(average))')])
            ->join(['t1' => $sub_select], new Expression('1'), [])
            ->where(['page.id' => $id]);

        return $this->selectWith($select);
    }
    
    /**
     * Execute Request Get users avgs for an organization
     *
     * @param int $id
     */
    public function getUsersAvg($id)
    {
        $grades_select =  $this->getGradesSelect($id);
        $sub_select = new Select();
        $sub_select->columns(['average' => new Expression('AVG(average)'), 'user_id' => 'item_user$user_id'])
            ->from(['g' => $grades_select])
            ->group('item_user$user_id');
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'page$average' => new Expression('t1.average')])
            ->join(['t1' => $sub_select], new Expression('1'), ['page$user_id' => 'user_id'])
            ->join(['r' => new Expression('(SELECT @rownum:=0)')], new Expression('1'), ['row_number' => new Expression('@rownum:=@rownum+1')])
            ->where(['page.id' => $id]);
        
        return $this->selectWith($select);
    }
    
     /**
      * Execute Request Get users avgs for an organization
      *
      * @param int $id
      * @param int $user_id
      */
    public function getUserGrades($id, $user_id)
    {
        $grades_select =  $this->getGradesSelect($id);
        $sub_select = new Select();
        $sub_select->columns(['average' => 'average', 'user_id' => 'item_user$user_id',  'page_id' => 'item$page_id'])
            ->from(['g' => $grades_select])
            ->where(['item_user$user_id ' => $user_id ]);
        $select = $this->tableGateway->getSql()->select();
        $select->columns([ 'page$id' => 't1.page_id', 'page$average' => 't1.average'])
            ->join(['t1' => $sub_select], new Expression('1'), [])
            ->join(['r' => new Expression('(SELECT @rownum:=0)')], new Expression('1'), ['row_number' => new Expression('@rownum:=@rownum+1')])
            ->where(['page.id' => $id]);
        
        return $this->selectWith($select);
    }
    
    /**
     * Execute Request Get users percentiles for an organization
     *
     * @param int $id
     */
    public function getUsersPrc($id)
    {
        $grades_select =  $this->getGradesSelect($id);
        $sub_select = new Select();
        $sub_select->columns(['average' => new Expression('AVG(average)'), 'user_id' => 'item_user$user_id'])
            ->from(['g' => $grades_select])
            ->group('item_user$user_id')
            ->order('average DESC');
        $sub_select2 = new Select();
        $sub_select2->from(['avg' => $sub_select])
            ->columns(['average', 'user_id'])
            ->join(['r' => new Expression('(SELECT @rownum:=0)')], new Expression('1'), ['row_number' => new Expression('MIN(@rownum:=@rownum+1)')])
            ->group('avg.average');

        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'page$percentile' => new Expression('FLOOR(COALESCE((MIN(t1.row_number)-1)*100/(MIN(t1.row_number) + @rownum-MAX(t1.row_number)),0))')])
            ->join(['t1' => $sub_select2], new Expression('1'), ['page$user_id' => 'user_id', 'page$average' => 'average'])
            ->where(['page.id' => $id])
            ->group('t1.average');

        return $this->selectWith($select);
    }
    
    public function getCount($me, $interval, $start_date = null, $end_date = null, $organization_id = null, $type = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns([ 'page$created_date' => new Expression('SUBSTRING(page.created_date,1,'.$interval.')'), 'page$count' => new Expression('COUNT(DISTINCT page.id)'), 'type'])
            ->where('page.deleted_date IS NULL')
            ->group([new Expression('SUBSTRING(created_date,1,'.$interval.')'), 'page.type']);

        if (null != $start_date) {
            $select->where(['page.created_date >= ? ' => $start_date]);
        }

        if (null != $end_date) {
            $select->where(['page.created_date <= ? ' => $end_date]);
        }

        if (null != $type) {
            $select->where(['page.type' => $type]);
        }
        
        if (null != $organization_id) {
            $select->join('user', 'page.user_id = user.id', [])
                ->join('page_user', 'user.id = page_user.user_id', [])
                ->where(['page_user.page_id' => $organization_id]);
        }
        
        return $this->selectWith($select);
    }

}
