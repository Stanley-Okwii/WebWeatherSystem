<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ArchiveScannedDekadalFormDataReportCopy extends CI_Controller {

    function __construct() {

        parent::__construct();
        error_reporting(E_PARSE);
        $this->load->model('DbHandler');
        $this->load->library('session');
        $this->load->library('encrypt');

    }
    public function index(){
        // $this->unsetflashdatainfo();
        $session_data = $this->session->userdata('logged_in');
        $userrole=$session_data['UserRole'];
        $userstation=$session_data['UserStation'];


        $query = $this->DbHandler->selectAll($userstation,'StationName','scannedarchivedekadalformreportcopydetails');  //value,field,table
        //  var_dump($query);
        if ($query) {
            $data['archivedscanneddekadalformreportdetails'] = $query;
        } else {
            $data['archivedscanneddekadalformreportdetails'] = array();
        }


        //Load the view.
        $this->load->view('archiveScannedDekadalFormDataReportCopy', $data);
    }

    public function DisplayFormToArchiveScannedDekadalFormReport(){
        $this->unsetflashdatainfo();
        $name='displaynewFormToArchiveScannedDekadalFormReport';
        $data['displaynewFormToArchiveScannedDekadalFormReport'] = array('name' => $name);

        //Get all Stations.
        $session_data = $this->session->userdata('logged_in');
        //$userrole=$session_data['UserRole'];
        $userstation=$session_data['UserStation'];

        $query = $this->DbHandler->selectAll($userstation,'StationName','stations');  //value,field,table
        //  var_dump($query);
        if ($query) {
            $data['stationsdata'] = $query;
        } else {
            $data['stationsdata'] = array();
        }

        /////////////////////////////////////////////////////////



        $this->load->view('archiveScannedDekadalFormDataReportCopy', $data);

    }
    public function DisplayFormToArchiveScannedDekadalFormReportForUpdate(){
        $this->unsetflashdatainfo();
        $session_data = $this->session->userdata('logged_in');
        $userstation=$session_data['UserStation'];

        $query = $this->DbHandler->selectAll($userstation,'StationName','stations');  //value,field,table
        //  var_dump($query);
        if ($query) {
            $data['stationsdata'] = $query;
        } else {
            $data['stationsdata'] = array();
        }




        $scanneddekadalformreportid = $this->uri->segment(3);

        $query = $this->DbHandler->selectById($scanneddekadalformreportid,'id','scannedarchivedekadalformreportcopydetails');  //$value, $field,$table
        if ($query) {
            $data['scanneddekadalformreportidDetails'] = $query;
        } else {
            $data['scanneddekadalformreportidDetails'] = array();
        }


        $this->load->view('archiveScannedDekadalFormDataReportCopy', $data);
    }


    public function insertInformationForArchiveScannedDekadalFormReport(){
        $this->unsetflashdatainfo();
        $session_data = $this->session->userdata('logged_in');
        $role=$session_data['UserRole'];

        $file_element_name = 'archievescannedcopy_dekadalformdatareport';


        $config['upload_path'] = 'archive/';
        // $config['upload_path'] = '/uploads/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf';
        $config['encrypt_name'] = FALSE;
        $config['max_size'] = '2048000';  // Can be set to particular file size , here it is 2 MB(2048 Kb)
        $config['max_height'] = '768';
        $config['max_width'] = '1024';

        $config['remove_spaces'] = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($file_element_name))
        {
            $status = 'error';
            echo   $msg = $this->upload->display_errors('', '');
        }
        else
        {
            $data = $this->upload->data();
            $filename = $data['file_name'];




            $formname = firstcharuppercase(chgtolowercase($this->input->post('formname_dekadal')));



                $station = $this->input->post('station_ArchiveScannedDekadalFormReport');
                $stationNo = $this->input->post('stationNo_ArchiveScannedDekadalFormReport');



            $FromdateOnScannedDekadalFormReport = $this->input->post('FromdateOnScannedDekadalFormReport_dekadal');
            $TodateOnScannedDekadalFormReport = $this->input->post('TodateOnScannedDekadalFormReport_dekadal');


            $description = $this->input->post('description_dekadal');
            $creationDate= date('Y-m-d H:i:s');
            $Approved="FALSE";
            $firstname=$session_data['FirstName'];
            $surname=$session_data['SurName'];
            $SubmittedBy=$user=$firstname.' '.$surname;

            $insertScannedDekadalFormReportDataDetails=array(
                'Form' => $formname, 'StationName' => $station,
                'StationNumber' => $stationNo, 'FromDate' => $FromdateOnScannedDekadalFormReport,'ToDate' => $TodateOnScannedDekadalFormReport,
                'Approved'=> $Approved,'SubmittedBy'=>$SubmittedBy,
                'Description'=>$description,'FileName' => $filename,'CreationDate'=> $creationDate);

            //$this->DbHandler->insertInstrument($insertInstrumentData);
            $insertsuccess= $this->DbHandler->insertData($insertScannedDekadalFormReportDataDetails,'scannedarchivedekadalformreportcopydetails'); //Array for data to insert then  the Table Name

            //Redirect the user back with  message
            if($insertsuccess){
                //Store User logs.
                //Create user Logs
                $session_data = $this->session->userdata('logged_in');
                $userrole=$session_data['UserRole'];
                $userstation=$session_data['UserStation'];
                $userstationNo=$session_data['StationNumber'];
                $name=$session_data['FirstName'].' '.$session_data['SurName'];

                $userlogs = array('Date'=>date('Y-m-d H:i:s'),'User' => $name,
                    'UserRole' => $userrole,'Action' => 'Added new Scanned Metar Form details',
                    'Details' => $name . ' added new Scanned Metar Form details into the system ',
                    'StationName' => $userstation,
                    'StationNumber' => $userstationNo ,
                    'IP' => $this->input->ip_address());
                //  save user logs
                // $this->DbHandler->saveUserLogs($userlogs);


                $this->session->set_flashdata('success', 'New Scanned Metar Form details info was added successfully!');
                $this->index();

            }
            else{
                $this->session->set_flashdata('error', '"Sorry, we encountered an issue! ');
                $this->index();

            }

        }
    }



    public function updateInformationForArchiveScannedDekadalFormReport(){
        $this->unsetflashdatainfo();

        $this->load->helper(array('form', 'url'));
        $session_data = $this->session->userdata('logged_in');
        $role=$session_data['UserRole'];

        $file_element_name = 'updatearchievescannedcopy_dekadalformdatareportcopy';


        $config['upload_path'] = 'archive/';
        // $config['upload_path'] = '/uploads/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf';
        $config['encrypt_name'] = FALSE;
        $config['max_size'] = '2048000';  // Can be set to particular file size , here it is 2 MB(2048 Kb)
        $config['max_height'] = '768';
        $config['max_width'] = '1024';

        $config['remove_spaces'] = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($file_element_name))
        {
            $status = 'error';
            echo   $msg = $this->upload->display_errors('', '');
        }
        else
        {
            $data = $this->upload->data();
            $filename = $data['file_name'];




            $formname = firstcharuppercase(chgtolowercase($this->input->post('formname')));


                $station = $this->input->post('station');
                $stationNo = $this->input->post('stationNo');




            $FromdateOnScannedDekadalFormReport = $this->input->post('FromdateOnScannedDekadalFormReport');
            $TodateOnScannedDekadalFormReport = $this->input->post('TodateOnScannedDekadalFormReport');


            $description = $this->input->post('description');

            $id = $this->input->post('id');






            $updateScannedDekadalFormReportDataDetails=array(
                'Form' => $formname, 'StationName' => $station,
                'StationNumber' => $stationNo, 'FromDate' => $FromdateOnScannedDekadalFormReport,'ToDate'=>$TodateOnScannedDekadalFormReport,
                'Description'=>$description,'FileName' => $filename,);

            //$this->DbHandler->insertInstrument($insertInstrumentData);
            $updatesuccess=$this->DbHandler->updateData($updateScannedDekadalFormReportDataDetails,'scannedarchivedekadalformreportcopydetails',$id);

            //Redirect the user back with  message
            if($updatesuccess){
                //Store User logs.
                //Create user Logs
                $session_data = $this->session->userdata('logged_in');
                $userrole=$session_data['UserRole'];
                $userstation=$session_data['UserStation'];
                $userstationNo=$session_data['StationNumber'];
                $name=$session_data['FirstName'].' '.$session_data['SurName'];

                $userlogs = array('Date'=>date('Y-m-d H:i:s'),'User' => $name,
                    'UserRole' => $userrole,'Action' => 'Added new Scanned Metar Form details',
                    'Details' => $name . ' added new Scanned Metar Form details into the system ',
                    'StationName' => $userstation,
                    'StationNumber' => $userstationNo ,
                    'IP' => $this->input->ip_address());
                //  save user logs
                // $this->DbHandler->saveUserLogs($userlogs);


                $this->session->set_flashdata('success', 'New Scanned Metar Form details info was added successfully!');
                $this->index();

            }
            else{
                $this->session->set_flashdata('error', '"Sorry, we encountered an issue! ');
                $this->index();

            }

        }

    }
    public function deleteInformationForArchiveScannedDekadalFormReport() {
        $this->unsetflashdatainfo();

        $id = $this->uri->segment(3); // URL Segment Three.

        $rowsaffected = $this->DbHandler->deleteData('scannedarchivedekadalformreportcopydetails',$id);  //$rowsaffected > 0

        if ($rowsaffected) {
            //Store User logs.
            //Create user Logs
            $session_data = $this->session->userdata('logged_in');
            $userrole=$session_data['UserRole'];
            $userstation=$session_data['UserStation'];
            $userstationNo=$session_data['StationNumber'];
            $name=$session_data['FirstName'].' '.$session_data['SurName'];

            $userlogs = array('Date'=>date('Y-m-d H:i:s'),'User' => $name,
                'UserRole' => $userrole,'Action' => 'Deleted instrument details',
                'Details' => $name . ' deleted instrument details into the system ',
                'StationName' => $userstation,
                'StationNumber' => $userstationNo ,
                'IP' => $this->input->ip_address());
            //  save user logs
            // $this->DbHandler->saveUserLogs($userlogs);

            $this->session->set_flashdata('success', 'Instrument info was deleted successfully!');
            $this->index();

            //redirect('/element', 'refresh');
        }
        else {

            $this->session->set_flashdata('error', '"Sorry, we encountered an issue! ');
            $this->index();

        }

    }
    function getInstruments($stationName) {  //Pass the StationName to get the Station Number.
        $this->load->helper(array('form', 'url'));

        $stationName = ($stationName == "") ? $this->input->post('stationName') : $stationName;
//check($value,$field,$table)
        if ($this->input->post('stationName') == "") {
            echo '<span style="color:#f00">Please Input Name. </span>';
        } else {


            //$get_result = $this->DbHandler->getResults($stationName, 'StationName', 'instruments');   // $value, $field, $table
            $data['results'] = $this->DbHandler->getResults($stationName, 'StationName', 'instruments');
            if($data['results']){   // we got a result, output json
                echo json_encode( $data['results'] );
            } else {
                echo json_encode( array('error' => true) );
            }



        }

    }
    ///Check DB against the DATE,STATIONName,StationNumber,TIME,METAR/SPECI OPTION
    function checkInDBIfArchiveScannedDekadalFormDataReportCopyRecordExistsAlready($fromdate,$todate,$stationName,$stationNumber) {  //Pass the StationName to get the Station Number.
        $this->load->helper(array('form', 'url'));

        $stationName = ($stationName == "") ? $this->input->post('stationName') : $stationName;
        $fromdate = ($fromdate == "") ? $this->input->post('fromdate') : $fromdate;
        $todate = ($todate == "") ? $this->input->post('todate') : $todate;
        $stationNumber = ($stationNumber == "") ? $this->input->post('stationNumber') : $stationNumber;

        //check($value,$field,$table)
        if ($this->input->post('stationName') == "") {
            echo '<span style="color:#f00">Please Input Name. </span>';
        }
        else {


            $get_result = $this->DbHandler->checkInDBIfArchiveScannedDekadalFormDataReportCopyRecordExistsAlready($fromdate,$todate,$stationName,$stationNumber,'scannedarchivedekadalformreportcopydetails');   // $value, $field, $table

            if( $get_result){
                echo json_encode($get_result);

            }
            else{

                echo json_encode($get_result);
            }
        }


    }
    public function unsetflashdatainfo(){

        if(isset($_SESSION['error'])){
            unset($_SESSION['error']);
        }

        elseif(isset($_SESSION['success'])){
            unset($_SESSION['success']);
        }
        elseif(isset($_SESSION['warning'])){
            unset($_SESSION['warning']);
        }
        elseif(isset($_SESSION['info'])){
            unset($_SESSION['info']);
        }

    }


}

?>