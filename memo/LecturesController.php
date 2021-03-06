<?php
namespace App\Controller\Supporter;

use App\Controller\Supporter\AppController;
use Cake\Routing\Router;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * Lectures Controller
 *
 * @property \App\Model\Table\LecturesTable $Lectures
 *
 * @method \App\Model\Entity\Lecture[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LecturesController extends AppController
{
    public $helpers = [
        'Paginator' => ['templates' => 'paginator-templates']
    ];
    
    public $components = ['Upload', 'Paginator'];
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $connection = ConnectionManager::get('default'); 
        $user = $this->Session->read('Auth.Supporter');
        
        $lectures_histories = $connection->execute('SELECT id,lecture_id,status FROM lectures_histories WHERE member_id = :id',['id' => $user["id"]])->fetchAll('assoc');
        
        $count = 1;
        foreach($lectures_histories as $lectures_historie){
            if($lectures_historie["status"] == 2){
                $count++;
            }
        }
        
        $data = $this->request->getQuery();
        
        $this->LecturesHistories = TableRegistry::get('lectures_histories');
        $subquery = $this->LecturesHistories->find('all')
            ->select(['member_id','lecture_id','status'])
            ->where(['member_id' => $user["id"]]);
        $list = $this->Lectures->findLecturesStatus($data)
        ->select(['id','title','message','movie_url'])
        ->join([
            'table' => $subquery,
            'alias' => 'lh',
            'type'  => 'LEFT',
            'conditions' => 'lh.lectures_histories__lecture_id = lectures.id',
        ]);
        
        $case = $list->newExpr()->addCase(
            [
                $list->newExpr()->add(['lectures_histories__status' => '2']),
                $list->newExpr()->add(['lectures_histories__status' => '1']),
                $list->newExpr()->add(['lectures_histories__status IS' => NULL]),
            ],
            [2,1,0],
            ['integer','integer','integer']
            );
        
        $list->select(['lectures_histories__status' => $case])->where(['display_flg' => '1'])->where(['lectures_histories__status' => 2]);
        
        $options = [
            'limit' => 10,
            'order' => ['id' => 'ASC'],
        ];
        
        $lectures = $this->paginate($list, $options);
        
        /*
        $count = 1;
        $first_flg = true;
        
        foreach($lectures as $lecture){
            if($lecture->lectures_histories["status"] == 2){
                $count++;
            }else{
                $first_flg = false;
                continue;
            }
        }
        
        if($count > 10){
            $options = [
                'limit' => 10,
                'order' => ['id' => 'ASC'],
            ];
        }else if($count <= 10 && $count > 0){
            $options = [
                'limit' => LIMIT_PER_PAGE,
                'order' => ['id' => 'ASC'],
            ];
        }
        
        $lectures = $this->paginate($list, $options);
        */
        
        $header = [
            'title' => __('Lecture'),
            'description' => '',
            'breadcrumbs' => [
                'Home' => Router::url(['controller' => 'Dashboards'], true),
                __('List {0}', __('Lecture')) => ''
            ]
        ];
        
        $next_flg = true;
        $this->set($data);
        $this->set(compact('lectures','user','header','next_flg'));
    }
}
