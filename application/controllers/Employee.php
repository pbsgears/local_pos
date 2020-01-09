<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** ================================
 * -- File Name : Employee.php
 * -- Project Name : Gs_SME
 * -- Module Name : Employee
 * -- Author : Nasik Ahamed
 * -- Create date : 02 - May 2016
 * -- Description :
 */
class Employee extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Employee_model');
        $this->load->helper('Employee');
        $this->load->helper('template_paySheet');
        $this->load->library('email_manual');

        ini_set('max_execution_time', 360);
        ini_set('memory_limit', '2048M');
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('hrms_approvals', $primaryLanguage);
        $this->lang->load('common', $primaryLanguage);
    }

    public function index()
    {

    }

    public function fetch_employees()
    {
        $employee_filter = '';
        $segment_filter = '';
        $employee = $this->input->post('employeeCode');
        $segment = $this->input->post('segment');
        $isDischarged = $this->input->post('isDischarged');
        if (!empty($employee) && $employee != 'null') {
            $employee = array($this->input->post('employeeCode'));
            $whereIN = "( " . join("' , '", $employee) . " )";
            $employee_filter = " AND EIdNo IN " . $whereIN;
        }
        if (!empty($segment) && $segment != 'null') {
            $segment = array($this->input->post('segment'));
            $whereIN = "( " . join("' , '", $segment) . " )";
            $segment_filter = " AND t1.segmentID IN " . $whereIN;
        }


        switch ($isDischarged) {
            case 'N':
                $discharged_filter = ' AND isDischarged != 1';
                break;

            case 'Y':
                $discharged_filter = ' AND isDischarged = 1';
                break;

            default:
                $discharged_filter = '';
        }

        $companyid = $this->common_data['company_data']['company_id'];
        $where = "t1.Erp_companyID = " . $companyid . $employee_filter . $segment_filter . $discharged_filter . "";
        $this->datatables->select('Ename2 AS empName, ECode, EDOJ, EIdNo, EmpImage,Ename2,EmpSecondaryCode,EpTelephone,DesDescription,srp_erp_segment.description as segment', false)
            ->from('srp_employeesdetails t1')
            ->join('srp_designation', 'DesignationID=t1.EmpDesignationId', 'LEFT')
            ->join('srp_erp_segment', 'srp_erp_segment.segmentID=t1.segmentID', 'LEFT')
            ->add_column('img', '<center><img class="" src="$1" style="width:30px;height: 20px;" ></center>', 'empImage(EmpImage)')
            ->add_column('emp_image', '$1', 'empMaster_action(EIdNo, empName)')
            ->add_column('action', '$1', 'empMaster_action(EIdNo, empName)')
            ->where('isSystemAdmin !=', 1)
            ->where($where);
        echo $this->datatables->generate();
    }

    public function fetch_reporting_manager_history()
    {
        $empId = trim($this->input->post('empId'));

        $this->datatables->select('employeeManagersID, t3.Ename2 AS modifiedUser,t1.modifiedDate,t2.Ename2', false)
            ->from('srp_erp_employeemanagers t1')
            ->join('srp_employeesdetails AS t2', 't1.managerID=t2.EIdNo')
            ->join('srp_employeesdetails AS t3', 't1.modifiedUserID=t3.EIdNo')
            ->where('t1.empID', $empId)
            ->where('t1.companyID', current_companyID());
        echo $this->datatables->generate();
    }

    public function new_employee()
    {

        $this->form_validation->set_rules('emp_title', 'Title', 'trim|required');
        //$this->form_validation->set_rules('initial', 'Initials', 'trim|required');
        $this->form_validation->set_rules('Ename4', 'Name', 'trim|required');
        $this->form_validation->set_rules('fullName', 'Full Name', 'trim|required');
        $this->form_validation->set_rules('emp_email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('emp_gender', 'Gender', 'trim|required');

        //Employee System Code Auto Generated Policy
        $isAutoGenerate = getPolicyValues('ECG', 'All');
        if($isAutoGenerate != 1){
            $this->form_validation->set_rules('EmpSecondaryCode', 'Secondary Code', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->new_employee());
        }
    }

    public function new_empSave()
    {

        $this->form_validation->set_rules('emp_title', 'Title', 'trim|required');
        //$this->form_validation->set_rules('initial', 'Initials', 'trim|required');
        $this->form_validation->set_rules('Ename4', 'Name', 'trim|required');
        $this->form_validation->set_rules('fullName', 'Full Name', 'trim|required');
        $this->form_validation->set_rules('emp_email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('emp_gender', 'Gender', 'trim|required');

        //Employee System Code Auto Generated Policy
        $isAutoGenerate = getPolicyValues('ECG', 'All');
        if($isAutoGenerate != 1){
            $this->form_validation->set_rules('EmpSecondaryCode', 'Secondary Code', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->new_empSave());
        }
    }

    public function update_employee()
    {

        $this->form_validation->set_rules('updateID', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('emp_title', 'Title', 'trim|required');
        $this->form_validation->set_rules('Ename4', 'Name', 'trim|required');
        //$this->form_validation->set_rules('initial', 'Initials', 'trim|required');
        $this->form_validation->set_rules('fullName', 'Full Name', 'trim|required');
        $this->form_validation->set_rules('emp_email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('emp_gender', 'Gender', 'trim|required');

        //Employee System Code Auto Generated Policy
        $isAutoGenerate = getPolicyValues('ECG', 'All');
        if($isAutoGenerate != 1){
            $this->form_validation->set_rules('EmpSecondaryCode', 'Secondary Code', 'trim|required');
        }

        /*if discharged*/
        $updateID = $this->input->post('updateID');
        $isDischarged = $this->db->query("SELECT isDischarged FROM srp_employeesdetails WHERE EIdNo = '$updateID'")->row('isDischarged');
        if ($isDischarged) {
            exit(json_encode(array('e', "<p>Employee is Discharged. You cannot edit.</p>")));
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $status = ['s'];

            if ($this->input->post('isConfirmed')) {
                
                $status = require_employeeDataStatus($updateID);
            }

            if ($status[0] == 'e') {
                echo json_encode(['e', 'Please fill following and then confirm <br/> ' . $status[1]]);
            } else {
                echo json_encode($this->Employee_model->update_employee());
            }

        }
    }

    public function new_employee_details()
    {
        $data = employee_details();
        echo json_encode($data);
    }

    public function employee_details()
    {
        echo json_encode($this->Employee_model->employee_details());
    }

    public function contactDetails_update()
    {

        $this->form_validation->set_rules('updateID', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('ep_address4', 'Permanent Country', 'trim|required');
        $this->form_validation->set_rules('ec_address4', 'Current Country', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->contactDetails_update());
        }
    }

    public function visaDetails_update()
    {
        $this->form_validation->set_rules('updateID', 'Employee ID', 'trim|required');
        $moreRecordCount = 0;
        foreach ($_POST as $key => $postData) {
            if ($key != 'updateID' && !empty($postData)) {
                $moreRecordCount++;
            }
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if ($moreRecordCount == 0) {
                die(json_encode(['e', 'There is no data to update']));

            }
            echo json_encode($this->Employee_model->visaDetails_update());
        }
    }

    public function visaDetails_update_envoy()
    {
        $this->form_validation->set_rules('updateID', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('contractStartDate', 'Start Date', 'trim|required|date');
        $this->form_validation->set_rules('contractEndDate', 'End Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            echo json_encode($this->Employee_model->visaDetails_update_envoy());
        }
    }

    public function new_empTitle()
    {
        $this->form_validation->set_rules('title', 'Title', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->new_empTitle());
        }
    }

    public function employee_rejoin()
    {
        $this->form_validation->set_rules('rejoinEmpID', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('rejoinDate', 'Rejoin date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->employee_rejoin());
        }
    }

    /*Start of Employee Employment */
    public function load_employmentView()
    {
        $empID = $this->input->post('empID');
        $template = trim($this->input->post('template'));
        $template = (!empty($template)) ? trim($template) : '';
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $data['empID'] = $empID;
        $data['isInitialLoad'] = 'Y';

        /*$data['isSalaryDeclared'] = $this->db->query("SELECT count(employeeNo) AS decCount FROM srp_erp_salarydeclarationdetails
                                                      WHERE companyID={$companyID} AND employeeNo={$empID}")->row('decCount');*/

        $data['isSalaryDeclared'] = 0;

        if ($template == 'envoy_') {
            $str = "LEFT JOIN(
                        SELECT empID AS contEmpID, contractID, contactTypeID, contractStartDate, contractEndDate, contractRefNo
                        FROM srp_erp_empcontracthistory WHERE companyID={$companyID} AND empID={$empID} AND isCurrent=1
                    ) AS contData ON contData.contEmpID=EIdNo";
            $subTB = 'contData.';
            $selectCol = ', contractID';
        } else {
            $str = $subTB = $selectCol = '';
        }

        $data['employmentData'] = $this->db->query("SELECT DATE_FORMAT(EDOJ,'{$convertFormat}') AS EDOJ, DATE_FORMAT(DateAssumed,'{$convertFormat}') AS DateAssumed,
                                                    payCurrency, payCurrencyID, segmentID, probationPeriod, isPayrollEmployee, EmployeeConType, typeID,
                                                    IF(ISNULL(probationPeriod),0,TIMESTAMPDIFF(MONTH, DateAssumed, probationPeriod)) AS probationPeriodMonth,
                                                    managerName, managerID, DATE_FORMAT({$subTB}contractStartDate,'{$convertFormat}') AS contractStartDate,
                                                    DATE_FORMAT({$subTB}contractEndDate,'{$convertFormat}') AS contractEndDate, {$subTB}contractRefNo,
                                                    DATE_FORMAT(probationPeriod,'{$convertFormat}') AS probationPeriodCnvt, manPowerNo, gradeID, pos_barCode,
                                                    DATE_FORMAT(EPassportExpiryDate,'{$convertFormat}') AS EPassportExpiryDate, AirportDestination,
                                                    EPassportNO, DATE_FORMAT(EVisaExpiryDate,'{$convertFormat}') AS EVisaExpiryDate {$selectCol}
                                                    FROM srp_employeesdetails AS empTB
                                                    LEFT JOIN(
                                                       SELECT empID, managerID, CONCAT(ECode, '_' ,Ename2) AS managerName FROM  srp_erp_employeemanagers
                                                       JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo=srp_erp_employeemanagers.managerID
                                                       WHERE empID={$empID} AND companyID={$companyID} AND active=1
                                                    )  AS managersTB ON managersTB.empID = empTB.EIdNo
                                                    {$str}
                                                    LEFT JOIN(
                                                      SELECT typeID, EmpContractTypeID FROM srp_empcontracttypes AS empConTyp
                                                      JOIN srp_erp_systememployeetype AS sysType ON sysType.employeeTypeID=empConTyp.typeID
                                                      WHERE Erp_CompanyID={$companyID}
                                                    ) AS sysContractType ON sysContractType.EmpContractTypeID = EmployeeConType
                                                    WHERE Erp_companyID={$companyID} AND EIdNo={$empID} ")->row_array();

        $this->load->view('system/hrm/ajax/' . $template . 'load_employmentView', $data);
        unset($data['employmentData']);


        $data['moreDesignation'] = $this->db->query("SELECT t1.DesignationID, t1.DesDescription FROM srp_designation t1 WHERE
                                                     Erp_companyID={$companyID} AND NOT EXISTS (
                                                           SELECT DesignationID FROM srp_employeedesignation WHERE
                                                           DesignationID = t1.DesignationID AND EmpID={$empID}
                                                         ) AND isDeleted!=1
                                                     ")->result_array();

        $data['empDesignationCount'] = $this->db->query("SELECT COUNT(DesignationID) usageCount FROM srp_employeedesignation
                                                         WHERE EmpID={$empID} AND isActive=1")->row('usageCount');
        $this->load->view('system/hrm/ajax/load_empDesignationView', $data);
        unset($data['empDesignationCount']);
        unset($data['moreDesignation']);

        $data['moreDepartment'] = $this->db->query("SELECT t1.DepartmentMasterID, t1.DepartmentDes FROM srp_departmentmaster t1 WHERE
                                                     Erp_companyID={$companyID} AND NOT EXISTS (
                                                           SELECT DepartmentMasterID FROM srp_empdepartments WHERE
                                                           DepartmentMasterID = t1.DepartmentMasterID AND EmpID={$empID}
                                                         )
                                                     AND isActive=1")->result_array();
        $this->load->view('system/hrm/ajax/load_empDepartmentView', $data);

    }

    public function getEmployeeJoinDate()
    {
        $empID = $this->input->post('empID');
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();

        $joinDate = $this->db->query("SELECT DATE_FORMAT(EDOJ,'{$convertFormat}') AS EDOJ FROM srp_employeesdetails
                                    WHERE Erp_companyID={$companyID} AND EIdNo={$empID} ")->row('EDOJ');
        echo json_encode(['s', $joinDate]);
    }

    public function save_employmentData()
    {
        $empID = $this->input->get('empID');
        $empConfirmedYN = isEmployeeConfirmed($empID);

        if ($empConfirmedYN == 1) {
            $this->form_validation->set_rules('empDoj', 'Date of joined', 'trim|date');
            $this->form_validation->set_rules('dateAssumed', 'Date Assumed', 'trim|required');
            $this->form_validation->set_rules('employeeConType', 'Employee Type', 'trim|required');
            $this->form_validation->set_rules('empCurrency', 'Currency', 'trim|required');
            $this->form_validation->set_rules('empSegment', 'Segment', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('e', validation_errors()));
            } else {
                echo json_encode($this->Employee_model->save_employmentData());
            }

        } else {
            $otherData = $this->input->post();
            $isValueSet = 0;

            foreach ($otherData as $key => $val) {
                if (!empty(trim($val))) {
                    $isValueSet = 1;
                    break;
                }
            }

            if ($isValueSet == 0) {
                echo json_encode(['e', 'There is no data to save']);
            } else {
                $dateAssumed = trim($this->input->post('dateAssumed'));
                $probationPeriod = trim($this->input->post('probationPeriod'));

                if (!empty($probationPeriod) && empty($dateAssumed)) {
                    exit(json_encode(['e', 'You can not add probation period without date assume']));
                }

                echo json_encode($this->Employee_model->save_employmentData());
            }
        }


    }

    public function save_employmentData_envoy()
    {
        $empID = $this->input->get('empID');
        $empConfirmedYN = isEmployeeConfirmed($empID);

        if ($empConfirmedYN == 1) {
            $this->form_validation->set_rules('empDoj', 'Date of joined', 'trim|date');
            $this->form_validation->set_rules('dateAssumed', 'Date Assumed', 'trim|required');
            $this->form_validation->set_rules('employeeConType', 'Employee Type', 'trim|required');
            $this->form_validation->set_rules('empCurrency', 'Currency', 'trim|required');
            $this->form_validation->set_rules('empSegment', 'Segment', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('e', validation_errors()));
            } else {
                echo json_encode($this->Employee_model->save_employmentData_envoy());
            }

        } else {
            $otherData = $this->input->post();
            $isValueSet = 0;

            foreach ($otherData as $key => $val) {
                if (!empty(trim($val))) {
                    $isValueSet = 1;
                    break;
                }
            }

            if ($isValueSet == 0) {
                echo json_encode(['e', 'There is no data to save']);
            } else {
                $dateAssumed = trim($this->input->post('dateAssumed'));
                $probationPeriod = trim($this->input->post('probationPeriod'));

                if (!empty($probationPeriod) && empty($dateAssumed)) {
                    exit(json_encode(['e', 'You can not add probation period without date assume']));
                }

                echo json_encode($this->Employee_model->save_employmentData_envoy());
            }
        }
    }

    public function fetch_contractHistory()
    {
        $empID = trim($this->input->post('empID'));
        $format = convert_date_format_sql();
        $companyID = current_companyID();

        $isContract = $this->db->query("SELECT typeID FROM srp_employeesdetails AS empTB
                                        LEFT JOIN(
                                            SELECT typeID, EmpContractTypeID FROM srp_empcontracttypes AS empConTyp
                                            JOIN srp_erp_systememployeetype AS sysType ON sysType.employeeTypeID=empConTyp.typeID
                                            WHERE Erp_CompanyID={$companyID}
                                       ) AS sysContractType ON sysContractType.EmpContractTypeID = EmployeeConType
                                       WHERE Erp_companyID={$companyID} AND EIdNo={$empID} ")->row('typeID');


        $this->datatables->select('contractID, DATE_FORMAT(contractStartDate,\'' . $format . '\') AS contractStartDate, IF(isCurrent=1, \'Yes\', \'No\') AS isCurrent1,
                          DATE_FORMAT(contractEndDate,\'' . $format . '\') AS contractEndDate, contractRefNo, isCurrent', false)
            ->from('srp_erp_empcontracthistory t1')
            ->add_column('isCurrentStr', '<div align="center">$1</div>', 'isCurrent1')
            ->add_column('action', '$1', 'action_contractHistory(contractID, ' . $isContract . ')')
            ->where('t1.empID', $empID)
            ->where('t1.companyID', $companyID);
        echo $this->datatables->generate();
    }

    function print_contractHistory()
    {
        $empID = trim($empID = trim($this->uri->segment(3)));
        $format = convert_date_format_sql();

        $data['master'] = $this->Employee_model->employee_details($empID);
        $data['history'] = $this->db->select('contractID, DATE_FORMAT(contractStartDate,\'' . $format . '\') AS contractStartDate,
                            IF(isCurrent=1, \'Yes\', \'No\') AS isCurrent1,
                            DATE_FORMAT(contractEndDate,\'' . $format . '\') AS contractEndDate, contractRefNo')
            ->from('srp_erp_empcontracthistory t1')
            ->where('t1.empID', $empID)
            ->where('t1.companyID', current_companyID())
            ->get()->result_array();

        $html = $this->load->view('system\hrm\print\contractHistory', $data, true);
        //echo $html;
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A5', 1);
    }

    function export_excelContractHistory()
    {

        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Employee Contact History');

        $header = ['Contract Start Date', 'Contract End Date ', 'Contract Ref No.', 'Is Current'];
        $empID = trim($empID = trim($this->uri->segment(3)));
        $format = convert_date_format_sql();

        $emp_data = $this->Employee_model->employee_details($empID);
        $history = $this->db->select('DATE_FORMAT(contractStartDate,\'' . $format . '\') AS contractStartDate,
                            DATE_FORMAT(contractEndDate,\'' . $format . '\') AS contractEndDate, contractRefNo,
                            IF(isCurrent=1, \'Yes\', \'No\') AS isCurrent1,')
            ->from('srp_erp_empcontracthistory t1')
            ->where('t1.empID', $empID)
            ->where('t1.companyID', current_companyID())
            ->get()->result_array();


        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 13,
                'name' => 'Calibri'
            )
        );

        $styleArray2 = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Calibri'
            )
        );
        $this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $this->excel->getActiveSheet()->fromArray([$emp_data['ECode'] . ' - ' . $emp_data['Ename1']], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray2);
        $this->excel->getActiveSheet()->fromArray(['Employee Contract History'], null, 'A3');
        $this->excel->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray2);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A5');
        $this->excel->getActiveSheet()->fromArray($history, null, 'A6');

        $filename = 'Employee Contact History.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    function getDate()
    {
        $period = $this->input->post('period');
        $empDoj = $this->input->post('empDoj');
        $date_format_policy = date_format_policy();
        $empDoj = input_format_date($empDoj, $date_format_policy);

        $date = date('Y-m-d', strtotime($empDoj . ' +' . $period . ' month'));
        $convertFormat = str_replace('%', '', convert_date_format_sql());
        $date = date($convertFormat, strtotime($date));

        echo json_encode([$date]);
    }

    function delete_empContract()
    {
        $contractID = trim($this->input->post('contractID'));

        $this->db->trans_start();
        $this->db->where(['companyID' => current_companyID(), 'contractID' => $contractID])->delete('srp_erp_empcontracthistory');
        $this->db->trans_complete();
        if ($this->db->trans_status() > 0) {
            $this->db->trans_commit();
            echo json_encode(['s', 'Employee Contract Details Deleted Successfully.']);
        } else {
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error In Employee Contract Details Deleting']);
        }
    }

    public function save_reportingManager()
    {
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('managerID', 'Manager', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_reportingManager());
        }
    }
    /*End of Employee Employment */

    /*Start of Employee Designation */
    public function load_empDesignationView()
    {
        $empID = $this->input->post('empID');
        $companyID = current_companyID();
        $data['empID'] = $empID;
        $data['isInitialLoad'] = 'N';
        $data['moreDesignation'] = $this->db->query("SELECT t1.DesignationID, t1.DesDescription FROM srp_designation t1 WHERE
                                                     Erp_companyID={$companyID} AND NOT EXISTS (
                                                           SELECT DesignationID FROM srp_employeedesignation WHERE
                                                           DesignationID = t1.DesignationID AND EmpID={$empID}
                                                         )
                                                     ")->result_array();

        $data['empDesignationCount'] = $this->db->query("SELECT COUNT(DesignationID) usageCount FROM srp_employeedesignation
                                                         WHERE EmpID={$empID} AND isActive=1")->row('usageCount');

        $this->load->view('system/hrm/ajax/load_empDesignationView', $data);
    }

    public function load_empDesignation_PDF_print()
    {
        $convertFormat = convert_date_format_sql();
        $empID = $this->input->post('empID');
        $companyID = current_companyID();
        $data['master'] = $this->Employee_model->employee_details($empID);
        $data['moreDesignation'] = $this->db->query('SELECT EmpDesignationID, startDate, endDate, isMajor, t1.DesignationID AS DesignationID, DesDescription, isActive,DATE_FORMAT(startDate,\'' . $convertFormat . '\') AS startDate_format, DATE_FORMAT(endDate,\'' . $convertFormat . '\') AS endDate_format FROM srp_employeedesignation AS t1 LEFT JOIN srp_designation AS t2 ON t1.DesignationID=t2.DesignationID WHERE EmpID = ' . $empID . ' AND isActive = 1 AND t1.Erp_companyID = ' . $companyID . '')->result_array();
        $html = $this->load->view('system/hrm/print/employeeDesignation_history_print', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4', $data);
    }

    public function fetch_empDesignations()
    {
        $convertFormat = convert_date_format_sql();

        $empID = $this->input->post('empID');
        $this->datatables->select('EmpDesignationID, startDate, endDate, isMajor, t1.DesignationID AS DesignationID, DesDescription, isActive,
                DATE_FORMAT(startDate,\'' . $convertFormat . '\') AS startDate_format, DATE_FORMAT(endDate,\'' . $convertFormat . '\') AS endDate_format,
                ')
            ->from('srp_employeedesignation AS t1')
            ->join('srp_designation AS t2', 't1.DesignationID=t2.DesignationID')
            ->add_column('edit', '$1', 'action_empDesignation(EmpDesignationID, DesDescription, isMajor)')
            ->add_column('isMajorAction', '$1', 'designation_status(DesignationID, isMajor)')
            ->add_column('isActiveAction', '$1', 'designationActive_status(EmpDesignationID, isActive)')
            ->where('EmpID', $empID)
            ->where('isActive', 1)
            ->where('t1.Erp_companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function save_empDesignations()
    {
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('designationID', 'Designation', 'trim|required');
        $this->form_validation->set_rules('startDate[]', 'Start Date', 'trim|date|required');
        $this->form_validation->set_rules('endDat', 'End Date', 'trim|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_empDesignations());
        }
    }

    public function edit_empDesignations()
    {
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('designationID-hidden', 'Designation', 'trim|required');
        $this->form_validation->set_rules('edit_startDate', 'Start Date', 'trim|required|date');
        $this->form_validation->set_rules('edit_endDate', 'End Date', 'trim|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->edit_empDesignations());
        }
    }


    public function delete_empDesignation()
    {
        $this->form_validation->set_rules('hidden-id', 'Designation ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->delete_empDesignation());
        }
    }

    public function changeEmpMajorDesignation()
    {
        $this->form_validation->set_rules('hidden-id', 'Designation ID', 'required');
        $this->form_validation->set_rules('empID', 'Employee ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->changeEmpMajorDesignation());
        }
    }

    public function changeActiveDesignation()
    {
        $this->form_validation->set_rules('hidden-id', 'Designation ID', 'required');
        $this->form_validation->set_rules('empID', 'Employee ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->changeActiveDesignation());
        }
    }

    /*End of Employee Designation */


    /*Start of Religion */
    public function fetch_religion()
    {
        $this->datatables->select('RId,Religion,
               IFNULL( (SELECT COUNT(EIdNo) FROM srp_employeesdetails WHERE Rid=t1.RId GROUP BY Rid), 0 ) AS usageCount')
            ->from('srp_religion AS t1')
            ->add_column('edit', '$1', 'action_religion(RId,Religion,usageCount)')
            ->where('Erp_companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function saveReligion()
    {
        $this->form_validation->set_rules('description[]', 'Religion', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->saveReligion());
        }
    }

    public function editReligion()
    {
        $this->form_validation->set_rules('religionDes', 'Religion', 'required');
        $this->form_validation->set_rules('hidden-id', 'Religion ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->editReligion());
        }
    }

    public function deleteReligion()
    {
        $this->form_validation->set_rules('hidden-id', 'Religion ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->deleteReligion());
        }
    }
    /*End of Religion */


    /*Start of Country */
    public function fetch_country()
    {
        $this->datatables->select('countryID, countryShortCode, CountryDes,
            IFNULL( (SELECT COUNT(EIdNo) FROM srp_employeesdetails WHERE EcAddress4=t1.countryID  OR EpAddress4=t1.countryID ), 0 ) AS usageCount')
            ->from('srp_countrymaster AS t1')
            ->add_column('edit', '$1', 'action_country(countryID, CountryDes, usageCount)')
            ->where('Erp_companyID', current_companyID());

        echo $this->datatables->generate();
    }

    /*public function fetch_allCountry(){
        $this->datatables->select('countryID, countryShortCode, CountryDes')
            ->from('srp_erp_countrymaster AS t1')
            ->add_column('edit', '$1', 'action_selectCountry(countryID, CountryDes, countryShortCode)')
            ->where('NOT EXISTS ( SELECT countryID FROM srp_countrymaster WHERE countryMasterID = t1.countryID
                        AND Erp_companyID ='.current_companyID().')');

        echo $this->datatables->generate();
    }*/

    public function fetch_allCountry()
    {
        $data['countries'] = $this->Employee_model->fetch_allCountry();
        $this->load->view('system/hrm/ajax/load_countries', $data);
    }

    public function saveCountry()
    {
        $this->form_validation->set_rules('country', 'Country', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->saveCountry());
        }
    }

    public function editCountry()
    {
        $this->form_validation->set_rules('religionDes', 'Religion', 'required');
        $this->form_validation->set_rules('hidden-id', 'Religion ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->editCountry());
        }
    }

    public function deleteCountry()
    {
        $this->form_validation->set_rules('hidden-id', 'Religion ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->deleteCountry());
        }
    }
    /*End of Country */


    /*Start of Designation */
    public function fetch_designation()
    {
        $this->datatables->select('DesignationID, DesDescription,
            IFNULL( (SELECT COUNT(EmpDesignationID) FROM srp_employeedesignation WHERE DesignationID=t1.DesignationID ), 0 ) AS usageCount')
            ->from('srp_designation AS t1')
            ->add_column('edit', '$1', 'action_designation(DesignationID, DesDescription, usageCount)')
            ->where('isDeleted', '0')
            ->where('Erp_companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function saveDesignation()
    {
        $this->form_validation->set_rules('description[]', 'Designation', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->saveDesignation());
        }
    }

    public function editDesignation()
    {
        $this->form_validation->set_rules('designationDes', 'Designation', 'required');
        $this->form_validation->set_rules('hidden-id', 'Designation ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->editDesignation());
        }
    }

    public function deleteDesignation()
    {
        $this->form_validation->set_rules('hidden-id', 'Designation ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->deleteDesignation());
        }
    }
    /*End of Designation */


    /*Start of Qualification */
    public function load_empQualificationView()
    {
        //$this->load->view('system/hrm/ajax/load_empQualificationView');

        $empID = $this->input->post('empID');
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $dateColumns = 'DATE_FORMAT(dateFrom,\'' . $convertFormat . '\') AS dateFromStr,';
        $dateColumns .= 'DATE_FORMAT(dateTo,\'' . $convertFormat . '\') AS dateToStr';
        $ed_details = $this->db->query("SELECT *, {$dateColumns} FROM srp_erp_employeeeducationaldetails AS t1
                                        JOIN srp_erp_degreetype AS t2 ON t2.degreeTypeID=t1.degree
                                        WHERE empID={$empID} AND companyID={$companyID}")->result_array();
        $data['details'] = $ed_details;

        $dateColumns = 'DATE_FORMAT(AwardedDate,\'' . $convertFormat . '\') AS awardedDateStr';
        $certification_data = $this->db->query("SELECT *, {$dateColumns} FROM srp_empcertification WHERE EmpID={$empID}")->result_array();
        $data['certification_data'] = $certification_data;

        $this->load->view('system/hrm/ajax/load_empEducationView', $data);
    }

    public function fetch_qualification()
    {
        $empID = $this->input->post('empID');
        $this->datatables->select('certificateID, Description, AwardedDate, GPA, Institution')
            ->from('srp_empcertification')
            ->add_column('edit', '$1', 'action_qualification(certificateID, Description)')
            ->where('EmpID', $empID);

        echo $this->datatables->generate();
    }

    public function saveQualification()
    {
        $this->form_validation->set_rules('certification', 'Certification', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->saveQualification());
        }
    }

    public function editQualification()
    {
        $this->form_validation->set_rules('certification', 'Qualification', 'required');
        $this->form_validation->set_rules('hidden-id', 'Qualification ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->editQualification());
        }
    }

    public function deleteQualification()
    {
        $this->form_validation->set_rules('hidden-id', 'Qualification ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->deleteQualification());
        }
    }
    /*End of Qualification */

    /***Start of Employee Academic ***/
    function saveAcademic(){

        $this->form_validation->set_rules('school', 'School', 'required');
        $this->form_validation->set_rules('degree', 'Degree', 'required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode( ['e', validation_errors()] ) );
        }

        $school = $this->input->post('school');
        $empID = $this->input->post('empID');
        $degree = $this->input->post('degree');
        $fieldOfStudy = $this->input->post('fieldOfStudy');
        $grade = $this->input->post('grade');
        $dateFrom = $this->input->post('dateFrom');
        $dateTo = $this->input->post('dateTo');
        $currentlyReadingYN = $this->input->post('currentlyReadingYN');
        $description = $this->input->post('description');
        $acc_society = $this->input->post('acc_society');
        $isFrom = $this->input->post('isFrom');
        $hrVerified = $this->input->post('isVerified');


        $date_format_policy = date_format_policy();
        $dateFrom = (!empty($dateFrom)) ? input_format_date($dateFrom, $date_format_policy) : null;
        $dateTo = (!empty($dateTo)) ? input_format_date($dateTo, $date_format_policy) : null;

        $data = array(
            'empID' => $empID,
            'school' => $school,
            'degree' => $degree,
            'fieldOfStudy' => $fieldOfStudy,
            'grade' => $grade,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'currentlyReadingYN' => $currentlyReadingYN,
            'description' => $description,
            'activitiesSocieties' => $acc_society,
            'companyID' => current_companyID(),
            'createdUserID' => current_userID(),
            'createdPCID' => current_pc(),
            'createdDateTime' => current_date()
        );

        if($isFrom != 'profile'){
            if($hrVerified == 1){
                $data['hrVerified'] = $hrVerified;
                $data['verifiedUserID'] = current_userID();
                $data['verifiedPC'] = current_pc();
                $data['verifiedDateTime'] = current_date();
            }

        }

        $int = $this->db->insert('srp_erp_employeeeducationaldetails', $data);

        if($int){
            echo json_encode(['s', 'Inserted successfully']);
        }
        else{
            $common_failed = $this->lang->line('common_failed');/* 'failed'*/
            echo json_encode(['e', $common_failed]);
        }
    }

    function updateAcademic(){

        $this->form_validation->set_rules('school', 'School', 'required');
        $this->form_validation->set_rules('degree', 'Degree', 'required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode( ['e', validation_errors()] ) );
        }

        $id = $this->input->post('hidden-id-acc');
        $school = $this->input->post('school');
        $empID = $this->input->post('empID');
        $degree = $this->input->post('degree');
        $fieldOfStudy = $this->input->post('fieldOfStudy');
        $grade = $this->input->post('grade');
        $dateFrom = $this->input->post('dateFrom');
        $dateTo = $this->input->post('dateTo');
        $currentlyReadingYN = $this->input->post('currentlyReadingYN');
        $description = $this->input->post('description');
        $acc_society = $this->input->post('acc_society');
        $isFrom = $this->input->post('isFrom');
        $hrVerified = $this->input->post('isVerified');

        $date_format_policy = date_format_policy();
        $dateFrom = (!empty($dateFrom)) ? input_format_date($dateFrom, $date_format_policy) : null;
        $dateTo = (!empty($dateTo)) ? input_format_date($dateTo, $date_format_policy) : null;

        $data = array(
            'empID' => $empID,
            'school' => $school,
            'degree' => $degree,
            'fieldOfStudy' => $fieldOfStudy,
            'grade' => $grade,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'currentlyReadingYN' => $currentlyReadingYN,
            'description' => $description,
            'activitiesSocieties' => $acc_society,
            'modifiedUserID' => current_userID(),
            'modifiedPCID' => current_pc(),
            'modifiedDateTime' => current_date()
        );

        if($isFrom != 'profile'){
            $companyID = current_companyID();
            $isAlreadyVerified = $this->db->query("SELECT hrVerified FROM srp_erp_employeeeducationaldetails
                                                       WHERE id={$id} AND companyID={$companyID}")->row('hrVerified');
            if($isAlreadyVerified != $hrVerified){
                if($hrVerified == 1){
                    $data['hrVerified'] = 1;
                    $data['verifiedUserID'] = current_userID();
                    $data['verifiedPC'] = current_pc();
                    $data['verifiedDateTime'] = current_date();
                }
                else{
                    $data['hrVerified'] = 0;
                    $data['verifiedUserID'] = null;
                    $data['verifiedPC'] = null;
                    $data['verifiedDateTime'] = null;
                }
            }
        }

        $this->db->where('id', $id)->update('srp_erp_employeeeducationaldetails', $data);

        echo json_encode(['s', 'Updated successfully']);
    }

    function deleteAcademic(){
        $id = $this->input->post('id');

        $del = $this->db->where('id', $id)->delete('srp_erp_employeeeducationaldetails');

        if($del){
            echo json_encode(['s', 'deleted successfully']);
        }
        else{
            $common_failed = $this->lang->line('common_failed');/* 'failed'*/
            echo json_encode(['e', $common_failed]);
        }
    }

    function getAcademicData(){
        $id = $this->input->post('id');
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $dateColumns = 'DATE_FORMAT(dateFrom,\'' . $convertFormat . '\') AS dateFromStr,';
        $dateColumns .= 'DATE_FORMAT(dateTo,\'' . $convertFormat . '\') AS dateToStr';
        $data = $this->db->query("SELECT *, {$dateColumns} FROM srp_erp_employeeeducationaldetails WHERE id={$id} AND companyID={$companyID}")->row_array();
        echo json_encode($data);
    }
    /***End of Employee Academic ***/


    /*Start of Document Setups */
    public function fetch_documentDescriptionMaster()
    {
        $this->datatables->select('t1.DocDesID as doc_ID, DocDescription as doc_Description, t1.SortOrder AS SortOrder, isMandatory,
                issueDate_req, expireDate_req, issuedBy_req')
            ->from('srp_documentdescriptionmaster AS t1')
            ->join('srp_documentdescriptionsetup AS t2', 't1.DocDesID=t2.DocDesID')
            ->add_column('edit', '$1', 'action_docSetup(doc_ID, doc_Description)')
            ->add_column('mandatory', '<center>$1</center>', 'mandatoryStatus(isMandatory)')
            ->add_column('st_issueDate_req', '<center>$1</center>', 'mandatoryStatus(issueDate_req)')
            ->add_column('st_expireDate_req', '<center>$1</center>', 'mandatoryStatus(expireDate_req)')
            ->add_column('st_issuedBy_req', '<center>$1</center>', 'mandatoryStatus(issuedBy_req)')
            ->where('t1.Erp_companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function save_documentDescriptions()
    {
        $this->form_validation->set_rules('description[]', 'Documents', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_documentDescriptions());
        }
    }

    public function edit_documentDescription()
    {
        $this->form_validation->set_rules('edit_description', 'Documents Description', 'trim|required');
        $this->form_validation->set_rules('hidden-id', 'Document ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->edit_documentDescription());
        }
    }

    public function delete_documentDescription()
    {
        $this->form_validation->set_rules('hidden-id', 'Documents ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->delete_documentDescription());
        }
    }


    public function fetch_documentSetups()
    {
        $this->datatables->select('t1.DocDesID as doc_ID, t1.DocDescription as doc_Description, isMandatory, t2.SortOrder AS SortOrder, DocDesSetupID')
            ->from('srp_documentdescriptionmaster AS t1')
            ->join('srp_documentdescriptionsetup AS t2', 't1.DocDesID=t2.DocDesID')
            ->add_column('mandatory', '<center>$1</center>', 'mandatoryStatus(isMandatory)')
            ->add_column('edit', '$1', 'action_docSetup(DocDesSetupID, doc_Description)')
            ->where('t2.Erp_companyID', current_companyID())
            ->where('FormType', 'EMP');

        echo $this->datatables->generate();
    }

    public function saveDoc_master()
    {
        $this->form_validation->set_rules('descriptionID[]', 'Documents', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->saveDoc_master());
        }
    }

    public function edit_document()
    {
        $this->form_validation->set_rules('edit_descriptionID', 'Documents Description', 'trim|required');
        $this->form_validation->set_rules('hidden-id', 'Document ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->edit_document());
        }
    }

    public function delete_DocSetup()
    {
        $this->form_validation->set_rules('hidden-id', 'Documents ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->delete_DocSetup());
        }
    }
    /*End of Document Setups */


    /*Start of Employee Document*/
    public function load_empDocumentView()
    {
        $docDet = $this->Employee_model->empDocument_setup();
        //echo '<pre>';print_r($docDet); echo '</pre>'; die();

        $data['docDet'] = $docDet;
        $data['isFromProfile'] = 'N';
        $this->load->view('system/hrm/ajax/load_empDocumentView', $data);
    }

    public function emp_documentSave()
    {
        $this->form_validation->set_rules('document', 'Document', 'trim|required');
        $this->form_validation->set_rules('docEmpID', 'Employee ID', 'trim|required');

        $documentID = $this->input->post('document');
        if(!empty($documentID)){
            $req_field = $this->db->get_where('srp_documentdescriptionsetup', ['DocDesID'=>$documentID])->row_array();

            if($req_field['issueDate_req'] == 1){
                $this->form_validation->set_rules('issueDate', 'Issue Date', 'trim|required');
            }

            if($req_field['expireDate_req'] == 1){
                $this->form_validation->set_rules('expireDate', 'Expire Date', 'trim|required');
            }

            if($req_field['issuedBy_req'] == 1){
                $issuedBy = $this->input->post('issuedBy');
                if($issuedBy == -1){
                    $this->form_validation->set_rules('issuedByText', 'Issue By', 'trim|required');
                }else{
                    $this->form_validation->set_rules('issuedBy', 'Issue By', 'trim|required');
                }
            }
        }


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->emp_documentSave());
        }
    }

    function emp_documentUpdate(){
        $this->form_validation->set_rules('documentType', 'Document', 'trim|required');
        $this->form_validation->set_rules('editID', 'Master ID', 'trim|required');


        $documentID = $this->input->post('documentType');
        if(!empty($documentID)){
            $req_field = $this->db->get_where('srp_documentdescriptionsetup', ['DocDesID'=>$documentID])->row_array();

            if($req_field['issueDate_req'] == 1){
                $this->form_validation->set_rules('issueDate', 'Issue Date', 'trim|required');
            }

            if($req_field['expireDate_req'] == 1){
                $this->form_validation->set_rules('expireDate', 'Expire Date', 'trim|required');
            }

            if($req_field['issuedBy_req'] == 1){
                $issuedBy = $this->input->post('issuedBy');
                if($issuedBy == -1){
                    $this->form_validation->set_rules('issuedByText', 'Issue By', 'trim|required');
                }else{
                    $this->form_validation->set_rules('issuedBy', 'Issue By', 'trim|required');
                }
            }
        }

        if ($this->form_validation->run() == FALSE) {
            die(json_encode(array('e', validation_errors())));
        }

        $companyID = current_companyID();
        $editID = $this->input->post('editID');
        $issueDate = $this->input->post('issueDate');
        $expireDate = $this->input->post('expireDate');
        $issuedBy = $this->input->post('issuedBy');
        $issuedByText = $this->input->post('issuedByText');
        $issuedByText = ($issuedBy == -1)? $issuedByText: null;


        $date_format_policy = date_format_policy();

        $issueDate = (!empty($issueDate)) ? input_format_date($issueDate, $date_format_policy) : null;
        $expireDate = (!empty($expireDate)) ? input_format_date($expireDate, $date_format_policy) : null;

        if($expireDate != null and $issueDate != null){
            if ($issueDate > $expireDate) {
                die(json_encode(['e', 'Issue date can not be greater than expire date']));
            }
        }

        $data = array(
            'issueDate' => $issueDate,
            'expireDate' => $expireDate,
            'issuedBy' => $issuedBy,
            'issuedByText' => $issuedByText,

            'ModifiedPC' => current_pc(),
            'ModifiedUserID' => current_userID(),
            'ModifiedUserName' => current_employee(),
            'ModifiedDateTime' => current_date()
        );

        $where = ['DocDesFormID'=>$editID, 'Erp_companyID' => $companyID];

        $this->db->trans_start();
        $this->db->where($where)->update('srp_documentdescriptionforms', $data);

        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            echo json_encode(['s', 'Document details updated successfully']);
        } else {
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in document details updated']);
        }


    }

    public function delete_empDocument()
    {
        $this->form_validation->set_rules('hidden-id', 'Document ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $hiddenID = $this->input->post('hidden-id');
            echo json_encode($this->Employee_model->delete_empDocument($hiddenID));
        }
    }
    /*End of Employee Document*/


    /*Start of Employee Department*/
    public function fetch_department()
    {
        $this->datatables->select('DepartmentMasterID, DepartmentDes, isActive,
            IFNULL( (SELECT count(DepartmentMasterID) FROM srp_empdepartments WHERE DepartmentMasterID=t1.DepartmentMasterID), 0 ) AS usageCount')
            ->from('srp_departmentmaster AS t1')
            ->add_column('status', '$1', 'confirm(isActive)')
            ->add_column('edit', '$1', 'action_department(DepartmentMasterID, DepartmentDes, isActive, usageCount)')
            ->where('Erp_companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function saveDepartment()
    {
        $this->form_validation->set_rules('department[]', 'Department', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->saveDepartment());
        }
    }

    public function editDepartment()
    {
        $this->form_validation->set_rules('departmentDes', 'Department', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('hidden-id', 'Department ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->editDepartment());
        }
    }

    public function deleteDepartment()
    {
        $this->form_validation->set_rules('hidden-id', 'Designation ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->deleteDepartment());
        }
    }
    /*End of Employee Department*/

    /*Start of Employee Department */
    public function load_empDepartmentsView()
    {
        $empID = $this->input->post('empID');
        $companyID = current_companyID();
        $data['empID'] = $empID;
        $data['isInitialLoad'] = 'N';
        $data['moreDepartment'] = $this->db->query("SELECT t1.DepartmentMasterID, t1.DepartmentDes FROM srp_departmentmaster t1 WHERE
                                                     Erp_companyID={$companyID} AND NOT EXISTS (
                                                           SELECT DepartmentMasterID FROM srp_empdepartments WHERE
                                                           DepartmentMasterID = t1.DepartmentMasterID AND EmpID={$empID}
                                                         )
                                                     AND isActive=1")->result_array();

        $this->load->view('system/hrm/ajax/load_empDepartmentView', $data);
    }

    public function fetch_empDepartments()
    {
        $empID = $this->input->post('empID');
        $this->datatables->select('EmpDepartmentID, t1.DepartmentMasterID AS DepartmentMasterID, DepartmentDes, t1.isActive AS isActive')
            ->from('srp_empdepartments AS t1')
            ->join('srp_departmentmaster AS t2', 't1.DepartmentMasterID=t2.DepartmentMasterID')
            ->add_column('edit', '$1', 'action_empDepartment(EmpDepartmentID, DepartmentDes)')
            ->add_column('status', '$1', 'department_status(EmpDepartmentID, isActive)')
            ->where('EmpID', $empID)
            ->where('t1.Erp_companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function save_empDepartments()
    {
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('items[]', 'Departments', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_empDepartments());
        }
    }

    public function delete_empDepartments()
    {
        $this->form_validation->set_rules('hidden-id', 'Departments ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->delete_empDepartments());
        }
    }

    public function statusChangeEmpDepartments()
    {
        $this->form_validation->set_rules('hidden-id', 'Departments ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->statusChangeEmpDepartments());
        }
    }
    /*End of Employee Department */


    /*Employee Salary details*/
    public function empSalaryDetails()
    {
        $empID = $this->input->post('empID');
        $empCurrency = $this->input->post('empCurrency');

        echo json_encode(
            array(
                0 => fetch_currency_desimal($empCurrency),
                1 => $this->Employee_model->loadEmpDeclarations($empID),
            )
        );
    }

    public function empSalaryDetailsView()
    {
        $companyID = current_companyID();
        $currentEmp = current_userID();
        $empID = $this->input->post('empID');

        $isGroupAccess = getPolicyValues('PAC', 'All');
        $access = 1;
        if($isGroupAccess == 1){
            if($currentEmp != $empID) {
                $access = $this->db->query("SELECT 1 AS acc FROM srp_erp_payrollgroupemployees AS empTB
                                    JOIN srp_erp_payrollgroupincharge AS inCharge ON inCharge.groupID=empTB.groupID
                                    WHERE empTB.companyID={$companyID} AND inCharge.companyID={$companyID}
                                    AND employeeID={$empID} AND empID={$currentEmp}")->row('acc');
            }
        }

        $data['access'] = $access;
        if($access == 1){
            $data['groupBYSalary'] = $this->db->query("SELECT id, salaryDescription, SUM(amount) AS amount, salaryCategoryType, transactionCurrency,
                                                   transactionCurrencyDecimalPlaces AS dPlaces
                                                   FROM srp_erp_pay_salarydeclartion AS declartionTB
                                                   JOIN srp_erp_pay_salarycategories AS catTB ON catTB.salaryCategoryID = declartionTB.salaryCategoryID
                                                   WHERE employeeNo={$empID} AND declartionTB.companyID={$companyID}
                                                   GROUP BY declartionTB.salaryCategoryID  ORDER BY salaryDescription ASC")->result();
            $empCurrency = $this->db->query("SELECT payCurrency FROM srp_employeesdetails WHERE EIdNo={$empID}")->row('payCurrency');
            $data['dPlaces'] = fetch_currency_desimal($empCurrency);
            $data['salaryDet'] = $this->Employee_model->loadEmpDeclarations($empID);
            $data['salaryDetNon'] = $this->Employee_model->loadEmpDeclarations_nonPayroll($empID);
        }


        $this->load->view('system/hrm/ajax/load_empSalaryDeclarations', $data);
    }


    /*Start of Employee shift*/
    public function load_empShiftView()
    {
        $empID = $this->input->post('empID');
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();

        $data['empID'] = $empID;
        $data['attendanceData'] = $this->db->query("SELECT leaveGroupID, isCheckin, empMachineID, floorID, overTimeGroup,
                                        IFNULL( (SELECT adjustmentDone FROM srp_erp_leavegroupchangehistory
                                        WHERE empID = empTB.EIdNo ORDER BY id DESC LIMIT 1), 1) AS leaveAdjustmentStatus
                                        FROM srp_employeesdetails AS empTB
                                        WHERE Erp_companyID={$companyID} AND EIdNo={$empID} ")->row_array();
        $data['history'] = [];
        $this->load->view('system/hrm/ajax/load_empShiftView', $data);
    }

    public function leave_group_change_history(){
        $companyID = current_companyID();
        $empID = $this->input->post('empID');
        $convertFormat = convert_date_format_sql();

        $this->datatables->select('t1.id AS id, description, DATE_FORMAT(t1.createdDateTime,\'' . $convertFormat . '\') AS crDate, 
            CASE
                WHEN adjustmentDone = 0 THEN \'Pending\'
                WHEN adjustmentDone = 1 THEN \'Done\'
                WHEN adjustmentDone = 2 THEN \'Initial\'
                WHEN adjustmentDone = 3 THEN \'Skipped \'
            END AS adjStatus, adjustmentDone
            ', false)
            ->from('srp_erp_leavegroupchangehistory AS t1')
            ->join('srp_erp_leavegroup AS t2', 't1.leaveGroupID=t2.leaveGroupID')
            ->add_column('action', '$1', 'leave_group_change_history_action(id,adjustmentDone)')
            ->where('empID', $empID)
            ->where('t1.companyID', $companyID);

        echo $this->datatables->generate();
    }

    public function save_attendanceData()
    {
        $empID = $this->input->get('empID');
        $empConfirmedYN = isEmployeeConfirmed($empID);

        if ($empConfirmedYN == 1) {
            $this->form_validation->set_rules('leaveGroupID', 'Leave group', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('e', validation_errors()));
            } else {

                $isLeaveGroupChangeConfirmed = $this->input->post('isLeaveGroupChangeConfirmed');
                if($isLeaveGroupChangeConfirmed == 0){

                    /*Check posted leave group match with current leave group*/
                    $leaveGroupID = $this->input->post('leaveGroupID');
                    $current_groupID = $this->db->query("SELECT leaveGroupID FROM srp_employeesdetails WHERE EIdNo={$empID} AND leaveGroupID={$leaveGroupID}")->row('leaveGroupID');

                    if(empty($current_groupID)){
                        /*leave adjustment status for last leave group change of this employee */
                        $adjustmentStatus = $this->db->query("SELECT adjustmentDone FROM srp_erp_leavegroupchangehistory WHERE empID={$empID} ORDER BY id DESC LIMIT 1")->row('adjustmentDone');

                        if($adjustmentStatus == 0){
                            die(json_encode(['e', 'Leave adjustment process was not processed for previous leave group change.<br/>
                                            Please process the adjustment and try again.']));
                        }

                        /*Check un approved leave available for this employee */
                        $leavePending = $this->db->query("SELECT COUNT(leaveMasterID) leaveCount FROM srp_erp_leavemaster WHERE empID = {$empID} 
                                            AND (confirmedYN = 0 OR confirmedYN = 1 AND approvedYN = 0)")->row('leaveCount');

                        if($leavePending > 0){
                            die(json_encode(['e', 'Please approve pending leave of this employee and try again.']));
                        }

                        die(json_encode(['m', 'Group change confirmation']));
                    }

                }
                echo json_encode($this->Employee_model->save_attendanceData());
            }

        } else {
            $otherData = $this->input->post();
            $isValueSet = 0;

            foreach ($otherData as $key => $val) {
                if (!empty(trim($val))) {
                    $isValueSet = 1;
                    break;
                }
            }

            if ($isValueSet == 0) {
                echo json_encode(['e', 'There is no data to save']);
            } else {
                echo json_encode($this->Employee_model->save_attendanceData());
            }
        }
    }

    public function create_leaveAdjustment_in_leave_group_change_view(){
        $companyID = current_companyID();
        $id = $this->input->post('id');

        $this->is_leave_group_change_leave_adjustment_processed($id);


        $historyData = $this->db->query("SELECT ECode, Ename2, t1.* FROM srp_erp_leavegroupchangehistory t1
                                        JOIN srp_employeesdetails empTB ON empTB.EIdNo=t1.empID
                                        WHERE t1.id={$id}")->row_array();

        $newGroupID = $historyData['leaveGroupID'];
        $empID = $historyData['empID'];
        $empName = $historyData['Ename2'];
        $empCode = $historyData['ECode'];

        $oldGrpID = $this->db->query("SELECT leaveGroupID FROM srp_erp_leavegroupchangehistory  WHERE  id < {$id} 
                             AND empID = {$empID} ORDER BY id DESC LIMIT 1")->row('leaveGroupID');

        $oldDes = $this->db->get_where('srp_erp_leavegroup', ['leaveGroupID'=>$oldGrpID])->row('description');
        $newDes = $this->db->get_where('srp_erp_leavegroup', ['leaveGroupID'=>$newGroupID])->row('description');

        if(empty($oldGrpID)){
            die( json_encode( ['e', 'Old leave group ID not found'] ) );
        }

        $oldGrpDet = $this->db->query("SELECT lvType.leaveTypeID, lvType.description ,gr_det.isCarryForward, gr_det.policyMasterID 
                        FROM srp_erp_leavegroup gr_tb
                        JOIN srp_erp_leavegroupdetails gr_det ON gr_tb.leaveGroupID = gr_det.leaveGroupID
                        JOIN srp_erp_leavetype lvType ON lvType.leaveTypeID = gr_det.leaveTypeID
                        WHERE gr_tb.leaveGroupID = {$oldGrpID} ORDER BY lvType.description ASC")->result_array();

        $newGrpDet = $this->db->query("SELECT lvType.leaveTypeID, lvType.description ,gr_det.isCarryForward, gr_det.policyMasterID, 
                        noOfDays, noOfHours
                        FROM srp_erp_leavegroup gr_tb
                        JOIN srp_erp_leavegroupdetails gr_det ON gr_tb.leaveGroupID = gr_det.leaveGroupID
                        JOIN srp_erp_leavetype lvType ON lvType.leaveTypeID = gr_det.leaveTypeID
                        WHERE gr_tb.leaveGroupID = {$newGroupID} ORDER BY lvType.description ASC")->result_array();

        /*** Get leave balance ***/
        $currentYearFirstDate = date('Y-01-01');
        $currentYearLastDate = date('Y-12-31');
        $str = '';

        foreach ($oldGrpDet as $rowOldGrp){
            $isCarryForward = $rowOldGrp['isCarryForward'];
            $policyMasterID = $rowOldGrp['policyMasterID'];
            $leaveGroupID = $oldGrpID;
            $lType = $rowOldGrp['leaveTypeID'];
            $desLeave = 'lev_'.$lType;

            if ($policyMasterID == 2) {

                $str .= "sum(if(leaveType='{$lType}',hoursEntitled,0)) - IFNULL( 
                          ( SELECT SUM(if(leaveTypeID={$lType},hours,0)) FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.empID = empTB.EIdNo AND approvedYN = 1 
                         ), 0 ) as '{$desLeave}',";
            }
            else {
                $carryForwardLogic = '';

                if ($policyMasterID == 1 ) {
                    $carryForwardLogic = ( $isCarryForward == 1 )? " ": " AND endDate BETWEEN '{$currentYearFirstDate}' AND '{$currentYearLastDate}'";
                }


                $str .= "sum( IF ( leaveType = {$lType}, daysEntitled, 0 ) ) - 
                                 IFNULL( ( SELECT SUM( IF (leaveTypeID = {$lType}, days, 0) ) FROM srp_erp_leavemaster WHERE 
                                 srp_erp_leavemaster.empID = empTB.EIdNo AND approvedYN = 1  {$carryForwardLogic} ), 0 ) AS '{$desLeave}', ";


            }
        }


        $leaveBalance = $this->db->query("SELECT {$str} empID
                     FROM srp_employeesdetails AS empTB
                     INNER JOIN srp_erp_leaveaccrualdetail AS entiDet ON empID = EIdNo
                     INNER JOIN srp_erp_leaveaccrualmaster AS entiMast ON entiDet.leaveaccrualMasterID = entiMast.leaveaccrualMasterID                     
                     AND entiMast.leaveGroupID = {$oldGrpID}
                     INNER JOIN `srp_erp_leavetype` ON srp_erp_leavetype.leaveTypeID = entiDet.leaveType
                     JOIN (
                        SELECT gMaster.leaveGroupID, leaveTypeID, policyMasterID, isCarryForward 
                        FROM srp_erp_leavegroup AS gMaster
                        JOIN srp_erp_leavegroupdetails AS gDet ON gMaster.leaveGroupID=gDet.leaveGroupID
                        WHERE companyID = {$companyID}
                     ) AS leaveTypeData ON leaveTypeData.leaveGroupID=entiMast.leaveGroupID  AND leaveTypeData.leaveTypeID = entiDet.leaveType
                     WHERE empTB.EIdNo={$empID} AND confirmedYN=1 AND
                     CASE  
                        WHEN ( leaveTypeData.isCarryForward=0 AND leaveTypeData.policyMasterID=1 ) 
                        THEN DATE_FORMAT( CONCAT(`year`,'-',LPAD(`month`,2,'00'),'-01'), '%Y-%m-%d') BETWEEN '{$currentYearFirstDate}' AND '{$currentYearLastDate}'
                        ELSE 1=1
                     END                                         
                     GROUP BY empID #ORDER BY Ename2 ")->row_array();


        $data['id'] = $id;
        $data['newGrpDet'] = $newGrpDet;
        $data['oldGrpDet'] = $oldGrpDet;
        $data['newDes'] = $newDes;
        $data['oldDes'] = $oldDes;
        $data['leaveBalance_arr'] = $leaveBalance;

        $view = $this->load->view('system/hrm/ajax/leave-adjustment-in-leave-group-change', $data, true);

        echo json_encode( ['s', '', 'view'=>$view] );
    }

    public function leaveAdjustment_in_leave_group_change(){
        $companyID = current_companyID();
        $id = $this->input->post('change-id');
        $dateTime = date('Y-m-d H:i:s');

        $this->is_leave_group_change_leave_adjustment_processed($id);


        $historyData = $this->db->query("SELECT CONCAT(ECode, ' - ', Ename2) empNameCode, t1.* 
                                        FROM srp_erp_leavegroupchangehistory t1
                                        JOIN srp_employeesdetails empTB ON empTB.EIdNo=t1.empID
                                        WHERE t1.id={$id}")->row_array();

        $newGroupID = $historyData['leaveGroupID'];
        $empID = $historyData['empID'];
        $empNameCode = $historyData['empNameCode'];

        /*** Start of old leave group adjustment ***/
        $oldGrpID = $this->db->query("SELECT leaveGroupID FROM srp_erp_leavegroupchangehistory  WHERE  id < {$id} 
                             AND empID = {$empID} ORDER BY id DESC LIMIT 1")->row('leaveGroupID');

        $oldGrpDet = $this->db->query("SELECT lvType.leaveTypeID, lvType.description ,gr_det.isCarryForward, gr_det.policyMasterID 
                        FROM srp_erp_leavegroup gr_tb
                        JOIN srp_erp_leavegroupdetails gr_det ON gr_tb.leaveGroupID = gr_det.leaveGroupID
                        JOIN srp_erp_leavetype lvType ON lvType.leaveTypeID = gr_det.leaveTypeID
                        WHERE gr_tb.leaveGroupID = {$oldGrpID} ORDER BY lvType.description ASC")->result_array();


        /*** Get leave balance ***/
        $currentYearFirstDate = date('Y-01-01');
        $currentYearLastDate = date('Y-12-31');
        $str = '';
        foreach ($oldGrpDet as $rowOldGrp){
            $isCarryForward = $rowOldGrp['isCarryForward'];
            $policyMasterID = $rowOldGrp['policyMasterID'];
            $lType = $rowOldGrp['leaveTypeID'];
            $desLeave = 'lev_'.$lType;

            if ($policyMasterID == 2) {

                $str .= "sum(if(leaveType='{$lType}',hoursEntitled,0)) - IFNULL( 
                          ( SELECT SUM(if(leaveTypeID={$lType},hours,0)) FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.empID = empTB.EIdNo AND approvedYN = 1 
                         ), 0 ) as '{$desLeave}',";
            }
            else {
                $carryForwardLogic = '';

                if ($policyMasterID == 1 ) {
                    $carryForwardLogic = ( $isCarryForward == 1 )? " ": " AND endDate BETWEEN '{$currentYearFirstDate}' AND '{$currentYearLastDate}'";
                }


                $str .= "sum( IF ( leaveType = {$lType}, daysEntitled, 0 ) ) - 
                                 IFNULL( ( SELECT SUM( IF (leaveTypeID = {$lType}, days, 0) ) FROM srp_erp_leavemaster WHERE 
                                 srp_erp_leavemaster.empID = empTB.EIdNo AND approvedYN = 1  {$carryForwardLogic} ), 0 ) AS '{$desLeave}', ";


            }
        }


        $leaveBalance_arr = $this->db->query("SELECT {$str} empID
                     FROM srp_employeesdetails AS empTB
                     INNER JOIN srp_erp_leaveaccrualdetail AS entiDet ON empID = EIdNo
                     INNER JOIN srp_erp_leaveaccrualmaster AS entiMast ON entiDet.leaveaccrualMasterID = entiMast.leaveaccrualMasterID                     
                     AND entiMast.leaveGroupID = {$oldGrpID}
                     INNER JOIN `srp_erp_leavetype` ON srp_erp_leavetype.leaveTypeID = entiDet.leaveType
                     JOIN (
                        SELECT gMaster.leaveGroupID, leaveTypeID, policyMasterID, isCarryForward 
                        FROM srp_erp_leavegroup AS gMaster
                        JOIN srp_erp_leavegroupdetails AS gDet ON gMaster.leaveGroupID=gDet.leaveGroupID
                        WHERE companyID = {$companyID}
                     ) AS leaveTypeData ON leaveTypeData.leaveGroupID=entiMast.leaveGroupID  AND leaveTypeData.leaveTypeID = entiDet.leaveType
                     WHERE empTB.EIdNo={$empID} AND confirmedYN=1 AND
                     CASE  WHEN ( leaveTypeData.isCarryForward=0 AND leaveTypeData.policyMasterID=1 ) 
                        THEN DATE_FORMAT( CONCAT(`year`,'-',LPAD(`month`,2,'00'),'-01'), '%Y-%m-%d') BETWEEN '{$currentYearFirstDate}' AND '{$currentYearLastDate}'
                        ELSE 1=1
                     END                                         
                     GROUP BY empID")->row_array();


        $this->load->library('sequence');
        $adjustment_master_data = $data = [
            'companyID' => $companyID,
            'documentID' => 'LAM',
            'year' => date('Y'),
            'month' => date('m'),
            'leaveGroupID' => $oldGrpID,
            'createdUserGroup' => current_user_group(),
            'createDate' => $dateTime,
            'createdpc' => current_pc(),
            'manualYN' => 1,
            'confirmedYN' => 1,
            'confirmedDate' => $dateTime,
            'confirmedby' => current_userID(),
        ];


        $adjustment_arr = [];
        $old_group_policyWise_leave_types = array_group_by($oldGrpDet, 'policyMasterID');

        $this->db->trans_start();

        if(!empty($old_group_policyWise_leave_types)){
            foreach ($old_group_policyWise_leave_types as $policyKey=>$old_group_policyWise_leave_row){

                $code = $this->sequence->sequence_generator('LAM');
                $description = 'Leave group change adjustment of [ '.$empNameCode.' ]';
                $adjustment_master_data['leaveaccrualMasterCode'] = $code;
                $adjustment_master_data['description'] = $description;
                $adjustment_master_data['policyMasterID'] = $policyKey;

                $this->db->insert('srp_erp_leaveaccrualmaster', $adjustment_master_data);
                $masterID = $this->db->insert_id();

                $adjustment_arr[] = [
                    'leaveChangeHistoryID' => $id,
                    'leaveAdjustmentID' => $masterID,
                    'isPrevious' => 1,
                    'companyID' => $companyID,
                    'createdPCID' => current_pc(),
                    'createdUserID' => current_userID(),
                    'createdDateTime' => $dateTime,
                    'createdUserName' => current_user(),
                ];

                $old_adjustment_data_det = [];
                foreach($old_group_policyWise_leave_row as $key=> $det){
                    $leaveID = $det['leaveTypeID'];
                    $leaveBalanceKey = 'lev_'.$leaveID;

                    $previous_balance = 0;
                    if(is_array($leaveBalance_arr)){
                        $previous_balance = (array_key_exists($leaveBalanceKey, $leaveBalance_arr))? $leaveBalance_arr[$leaveBalanceKey]: 0;
                    }

                    $daysEntitled = 0;
                    $hoursEntitled = 0;
                    if($policyKey == 2){ /*** Hourly ***/
                        // Have to discuss and implement
                    }
                    else{ /*** Annually/ Monthly ***/
                        $daysEntitled = 0 - $previous_balance;
                    }


                    $old_adjustment_data_det[$key]['leaveaccrualMasterID'] = $masterID;
                    $old_adjustment_data_det[$key]['empID'] = $empID;
                    $old_adjustment_data_det[$key]['leaveGroupID'] = $oldGrpID;
                    $old_adjustment_data_det[$key]['leaveType'] = $leaveID;
                    $old_adjustment_data_det[$key]['daysEntitled'] = $daysEntitled;
                    $old_adjustment_data_det[$key]['hoursEntitled'] = $hoursEntitled;
                    $old_adjustment_data_det[$key]['previous_balance'] = $previous_balance;
                    $old_adjustment_data_det[$key]['description'] = $description;
                    $old_adjustment_data_det[$key]['createdUserGroup'] = current_user_group();
                    $old_adjustment_data_det[$key]['createdPCid'] = current_pc();
                }

                $this->db->insert_batch('srp_erp_leaveaccrualdetail', $old_adjustment_data_det);

            }
        }


        /*** Start of new leave group adjustment ***/
        $newGrpDet = $this->db->query("SELECT lvType.leaveTypeID, gr_det.policyMasterID 
                        FROM srp_erp_leavegroup gr_tb
                        JOIN srp_erp_leavegroupdetails gr_det ON gr_tb.leaveGroupID = gr_det.leaveGroupID
                        JOIN srp_erp_leavetype lvType ON lvType.leaveTypeID = gr_det.leaveTypeID
                        WHERE gr_tb.leaveGroupID = {$newGroupID} ORDER BY lvType.description ASC")->result_array();

        $newLeaveType = $this->input->post('newLeaveType');
        $adjustmentVal = $this->input->post('adjustmentVal');

        $new_LeaveType_base_adjustVal = [];
        foreach ($newLeaveType as $key=>$lType){
            $new_LeaveType_base_adjustVal[$lType] = $adjustmentVal[$key];
        }


        $new_group_policyWise_leave_types = array_group_by($newGrpDet, 'policyMasterID');

        if(!empty($new_group_policyWise_leave_types)){
            $adjustment_master_data['leaveGroupID'] = $newGroupID;
            foreach ($new_group_policyWise_leave_types as $policyKey=>$new_group_policyWise_leave_row){

                $code = $this->sequence->sequence_generator('LAM');
                $description = 'Leave group change adjustment of [ '.$empNameCode.' ]';
                $adjustment_master_data['leaveaccrualMasterCode'] = $code;
                $adjustment_master_data['description'] = $description;
                $adjustment_master_data['policyMasterID'] = $policyKey;

                $this->db->insert('srp_erp_leaveaccrualmaster', $adjustment_master_data);
                $masterID = $this->db->insert_id();

                $adjustment_arr[] = [
                    'leaveChangeHistoryID' => $id,
                    'leaveAdjustmentID' => $masterID,
                    'isPrevious' => 0,
                    'companyID' => $companyID,
                    'createdPCID' => current_pc(),
                    'createdUserID' => current_userID(),
                    'createdDateTime' => $dateTime,
                    'createdUserName' => current_user(),
                ];

                $new_adjustment_data_det = [];
                foreach($new_group_policyWise_leave_row as $key=> $det){
                    $leaveID = $det['leaveTypeID'];
                    $daysEntitled = 0;
                    $hoursEntitled = 0;

                    if($policyKey == 2){ /*** Hourly ***/
                        $hoursEntitled = (array_key_exists($leaveID, $new_LeaveType_base_adjustVal)) ? $new_LeaveType_base_adjustVal[$leaveID]: 0;
                    }
                    else{ /*** Annually/ Monthly ***/
                        $daysEntitled = (array_key_exists($leaveID, $new_LeaveType_base_adjustVal)) ? $new_LeaveType_base_adjustVal[$leaveID]: 0;
                    }


                    $new_adjustment_data_det[$key]['leaveaccrualMasterID'] = $masterID;
                    $new_adjustment_data_det[$key]['empID'] = $empID;
                    $new_adjustment_data_det[$key]['leaveGroupID'] = $newGroupID;
                    $new_adjustment_data_det[$key]['leaveType'] = $leaveID;
                    $new_adjustment_data_det[$key]['daysEntitled'] = $daysEntitled;
                    $new_adjustment_data_det[$key]['hoursEntitled'] = $hoursEntitled;
                    $new_adjustment_data_det[$key]['previous_balance'] = 0;
                    $new_adjustment_data_det[$key]['description'] = $description;
                    $new_adjustment_data_det[$key]['createdUserGroup'] = current_user_group();
                    $new_adjustment_data_det[$key]['createdPCid'] = current_pc();
                }

                $this->db->insert_batch('srp_erp_leaveaccrualdetail', $new_adjustment_data_det);
            }
        }

        /*** Leave group change history adjustment master record insert ***/
        $this->db->insert_batch('srp_erp_leavegroupchangehistoryadjustment', $adjustment_arr);

        $updateData = [
            'adjustmentDone' => 1,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => current_date(),
        ];

        $this->db->where(['id'=>$id, 'companyID'=>$companyID])->update('srp_erp_leavegroupchangehistory', $updateData);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Leave adjustment process successfully updated.']);
        }else{
            echo json_encode(['e', 'Error in leave adjustment process.']);
        }
    }

    public function view_leaveAdjustment_in_leave_group_change(){
        $id = $this->input->post('id');
        $companyID = current_companyID();

        $oldGrpDet = $this->db->query("SELECT leaveTypeID, t4.description, previous_balance, t2.leaveGroupID
                                FROM srp_erp_leavegroupchangehistoryadjustment t1
                                JOIN srp_erp_leaveaccrualmaster t2 ON t1.leaveAdjustmentID = t2.leaveaccrualMasterID
                                JOIN srp_erp_leaveaccrualdetail t3 ON t2.leaveaccrualMasterID = t3.leaveaccrualMasterID
                                JOIN srp_erp_leavetype t4 ON t4.leaveTypeID = t3.leaveType
                                WHERE t1.companyID={$companyID} AND leaveChangeHistoryID = {$id} AND t1.isPrevious = 1 
                                ORDER BY t4.description")->result_array();

        $newGrpDet = $this->db->query("SELECT leaveTypeID, t4.description, daysEntitled, t2.leaveGroupID
                                FROM srp_erp_leavegroupchangehistoryadjustment t1
                                JOIN srp_erp_leaveaccrualmaster t2 ON t1.leaveAdjustmentID = t2.leaveaccrualMasterID
                                JOIN srp_erp_leaveaccrualdetail t3 ON t2.leaveaccrualMasterID = t3.leaveaccrualMasterID
                                JOIN srp_erp_leavetype t4 ON t4.leaveTypeID = t3.leaveType
                                WHERE t1.companyID={$companyID} AND leaveChangeHistoryID = {$id} AND t1.isPrevious = 0 
                                ORDER BY t4.description")->result_array();

        $oldGrpID = $oldGrpDet[0]['leaveGroupID'];
        $newGroupID = $newGrpDet[0]['leaveGroupID'];
        $oldDes = $this->db->get_where('srp_erp_leavegroup', ['leaveGroupID'=>$oldGrpID])->row('description');
        $newDes = $this->db->get_where('srp_erp_leavegroup', ['leaveGroupID'=>$newGroupID])->row('description');

        $data['newDes'] = $newDes;
        $data['oldDes'] = $oldDes;
        $data['oldGrpDet'] = $oldGrpDet;
        $data['newGrpDet'] = $newGrpDet;

        echo $this->load->view('system/hrm/ajax/leave-adjustment-in-leave-group-change-view', $data, true);
    }

    public function is_leave_group_change_leave_adjustment_processed($id){
        $isProcessed = $this->db->get_where('srp_erp_leavegroupchangehistory', ['id'=>$id])->row('adjustmentDone');

        if($isProcessed == 1){
            die( json_encode( ['e', 'Already leave adjustment processed for this group change.'] ) );
        }

        if($isProcessed == 2){
            die( json_encode( ['e', 'You can not process leave adjustment for initial leave group change.'] ) );
        }

        if($isProcessed == 3){
            die( json_encode( ['e', 'Leave adjustment for this leave group change is already skipped.'] ) );
        }
    }

    public function skipLeaveAdjustment(){
        $companyID = current_companyID();
        $id = $this->input->post('adjID');

        $this->is_leave_group_change_leave_adjustment_processed($id);

        $updateData = [
            'adjustmentDone' => 3,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => current_date(),
        ];

        $this->db->where(['id'=>$id, 'companyID'=>$companyID])->update('srp_erp_leavegroupchangehistory', $updateData);

        echo json_encode( ['s', 'Adjustment skipped successfully.'] );
    }

    public function load_social_incusrance()
    {
        $empID = $this->input->post('empID');
        $data['empID'] = $empID;
        $companyID = current_companyID();

        $data['ssoNo'] = $this->db->get_where('srp_employeesdetails', ['EIdNo'=>$empID])->row('ssoNo');

        $data['socialInsurances'] = $this->db->query("SELECT ssoDet.socialInsuranceDetailID, ssoMas.Description, ssoDet.socialInsuranceNumber 
                                      FROM srp_erp_socialinsurancemaster ssoMas 
                                      INNER JOIN srp_erp_socialinsurancedetails ssoDet ON ssoMas.socialInsuranceID = ssoDet.socialInsuranceMasterID 
                                      WHERE ssoDet.empID = '{$empID}' AND ssoDet.companyID = '{$companyID}'")->result_array();

        $data['si'] = $this->db->query("SELECT srp_erp_socialinsurancemaster.socialInsuranceID, srp_erp_socialinsurancemaster.Description, 'sso' AS type
                                      FROM srp_erp_socialinsurancemaster WHERE companyID = '{$companyID}'
                                      UNION 
                                      SELECT payeeMasterID AS socialInsuranceID, srp_erp_payeemaster.description AS description, 'Payee' AS type
                                      FROM srp_erp_payeemaster WHERE srp_erp_payeemaster.companyID ='{$companyID}'")->result_array();

        $this->load->view('system/hrm/ajax/load_socialInsurance', $data);
    }

    function ajax_update_ssoNo(){
        $value = $this->input->post('value');
        $empID = $this->input->post('pk');
        $data = ['ssoNo'=>$value];

        $this->db->trans_start();

        $this->db->where(['EIdNo'=>$empID])->update('srp_employeesdetails', $data);
        $rptVal = ['reportValue'=>$value, 'timestamp'=>current_date(true)];
        $this->db->where(['reportID'=>4,'empID'=>$empID])->update('srp_erp_sso_reporttemplatedetails', $rptVal);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Updated successfully.']);
        }else{
            echo json_encode(['e', 'Error in update process.']);
        }

    }

    public function fetch_empShifts()
    {
        $empID = $this->input->post('empID');
        $convertFormat = convert_date_format_sql();
        $details = '<div align="right" >';
        $details .= '<span class="glyphicon glyphicon-pencil" onclick="editEmp_shift(this)" style="color:#3c8dbc;"></span>&nbsp;&nbsp; | &nbsp;&nbsp;';
        $details .= '<span class="glyphicon glyphicon-trash traceIcon" onclick="deleteEmp_shift(this)" style="color:#d15b47;"></span>';
        $details .= '</div>';

        $this->datatables->select('autoID, Description, shiftEmp.shiftID AS shiftID,
            DATE_FORMAT(startDate,\'' . $convertFormat . '\') startDate, DATE_FORMAT(endDate,\'' . $convertFormat . '\') endDate ', false)
            ->from('srp_erp_pay_shiftemployees AS shiftEmp')
            ->join('srp_erp_pay_shiftmaster AS shiftMaster', 'shiftMaster.shiftID=shiftEmp.shiftID')
//            ->add_column('status', '$1', 'confirm(isActive)')
            ->add_column('edit', $details)
            ->where('empID', $empID);

        echo $this->datatables->generate();
    }

    public function save_empShift()
    {
        $this->form_validation->set_rules('shiftID', 'Shift ID', 'trim|required');
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required');
        //$this->form_validation->set_rules('startDate', 'Start Date', 'trim|required|date');
        //$this->form_validation->set_rules('endDate', 'End Date', 'trim|required|date|callback_validateEmpShiftDate');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_empShift());
        }
    }

    function validateEmpShiftDate()
    {
        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');

        if ($startDate < $endDate) {
            return true;
        } else {
            $this->form_validation->set_message('validateEmpShiftDate', 'End date should be greater than start date');
            return false;
        }
    }

    public function update_empShift()
    {
        $this->form_validation->set_rules('shiftID', 'Shift ID', 'trim|required');
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('editID', 'Shift ID', 'trim|required');
        //$this->form_validation->set_rules('startDate', 'Start Date', 'trim|required|date');
        //$this->form_validation->set_rules('endDate', 'End Date', 'trim|required|date|callback_validateEmpShiftDate');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->update_empShift());
        }
    }

    public function delete_empShift()
    {

        $this->form_validation->set_rules('hidden-id', 'Shift ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->delete_empShift());
        }
    }
    /*End of Employee shift*/


    /*Start of Employee Floor*/
    public function fetch_floor()
    {
        $this->datatables->select('floorID, floorDescription, isActive,
            IFNULL( (SELECT count(floorID) FROM srp_employeesdetails WHERE floorID=t1.floorID), 0 ) AS usageCount')
            ->from('srp_erp_pay_floorMaster AS t1')
            ->add_column('status', '$1', 'confirm(isActive)')
            ->add_column('edit', '$1', 'action_floor(floorID, floorDescription, isActive, usageCount)')
            ->where('companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function saveFloor()
    {
        $this->form_validation->set_rules('floor[]', 'Floor', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->saveFloor());
        }
    }

    public function editFloor()
    {
        $this->form_validation->set_rules('floorDes', 'Floor', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('hidden-id', 'Floor ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->editFloor());
        }
    }

    public function deleteFloor()
    {
        $this->form_validation->set_rules('hidden-id', 'Floor ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->deleteFloor());
        }
    }
    /*End of Employee Floor*/


    /*Start of Employee Bank*/
    public function fetch_empBank()
    {
        $details = '<div align="right" >';
        $details .= '<i rel="tooltip" class="fa fa-sitemap" aria-hidden="true" onclick="branches(this)" title="Branches"></i>&nbsp;&nbsp; | &nbsp;&nbsp;';
        $details .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" onclick="edit_empBank(this)" style="color:#3c8dbc;"></span>&nbsp;&nbsp; | &nbsp;&nbsp;';
        $details .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash traceIcon" onclick="delete_empBank(this)" style="color:#d15b47;"></span>';
        $details .= '</div>';

        $this->datatables->select('bankID, bankCode, bankName,
            IFNULL( (SELECT count(bankID) FROM srp_erp_pay_salaryaccounts WHERE bankID=t1.bankID), 0 ) AS usageCount')
            ->from('srp_erp_pay_bankmaster AS t1')
            ->add_column('edit', $details)
            ->where('companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function save_empBank()
    {
        $this->form_validation->set_rules('bankCode', 'Bank Code', 'required|trim');
        $this->form_validation->set_rules('bankName', 'Bank Name', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_empBank());
        }
    }

    public function update_empBank()
    {
        $this->form_validation->set_rules('bankCode', 'Bank Code', 'required|trim');
        $this->form_validation->set_rules('bankName', 'Bank Name', 'required|trim');
        $this->form_validation->set_rules('hiddenID', 'Bank Mater ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->update_empBank());
        }
    }

    public function delete_empBank()
    {
        $this->form_validation->set_rules('hiddenID', 'Bank master ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->delete_empBank());
        }
    }


    public function fetch_empBankBranches()
    {
        $bankID = $this->input->post('bankID');
        $details = '<div align="right" >';
        $details .= '<span class="glyphicon glyphicon-pencil" onclick="edit_empBranchBank(this)" style="color:#3c8dbc;"></span>&nbsp;&nbsp; | &nbsp;&nbsp;';
        $details .= '<span class="glyphicon glyphicon-trash traceIcon" onclick="delete_empBranchBank(this)" style="color:#d15b47;"></span>';
        $details .= '</div>';

        $this->datatables->select('branchID, bankID, branchName, branchCode, swiftCode,
            IFNULL( (SELECT count(bankID) FROM srp_erp_pay_salaryaccounts WHERE branchID=t1.branchID), 0 ) AS usageCount')
            ->from('srp_erp_pay_bankbranches AS t1')
            ->add_column('edit', $details)
            ->where('bankID', $bankID)
            ->where('companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function save_empBranchBank()
    {
        $this->form_validation->set_rules('branchCode', 'Branch Code', 'required|trim');
        $this->form_validation->set_rules('branchName', 'Branch Name', 'required|trim');
        $this->form_validation->set_rules('bankID', 'Bank ID', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_empBranchBank());
        }
    }

    public function update_empBranchBank()
    {
        $this->form_validation->set_rules('branchCode', 'Branch Code', 'required|trim');
        $this->form_validation->set_rules('branchName', 'Branch Name', 'required|trim');
        $this->form_validation->set_rules('bankID', 'Bank ID', 'required|trim');
        $this->form_validation->set_rules('hiddenID', 'Branch ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->update_empBranchBank());
        }
    }

    public function delete_empBranchBank()
    {
        $this->form_validation->set_rules('hiddenID', 'BranchID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->delete_empBranchBank());
        }
    }
    /*End of Employee Bank*/


    /*Start of Over time*/
    public function fetch_OTCat()
    {
        $details = '<div align="right" >';
        $details .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" onclick="edit_OTCat(this)" style="color:#3c8dbc;"></span>&nbsp;&nbsp; | &nbsp;&nbsp;';
        $details .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash traceIcon" onclick="delete_OTCat(this)" style="color:#d15b47;"></span>';
        $details .= '</div>';

        $this->datatables->select('t1.ID, OTMasterID, description, catDescription, IFNULL( (SELECT count(floorID) FROM srp_employeesdetails WHERE floorID=t1.ID), 0 ) AS usageCount,t3.salaryDescription as salaryDescription,t3.salaryCategoryID as salaryCategoryID')
            ->from('srp_erp_pay_overtimecategory AS t1')
            ->join('srp_erp_pay_sys_overtimecategory AS t2', 't2.ID=t1.OTMasterID')
            ->join('srp_erp_pay_salarycategories AS t3', 't3.salaryCategoryID=t1.salaryCategoryID', 'left')
            ->where('t1.companyID', current_companyID())
            ->add_column('status', '$1', 'confirm(isActive)')
            ->add_column('edit', $details);


        echo $this->datatables->generate();
    }

    public function saveOTCat()
    {
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('masterCat', 'Mater Category', 'required');
        $this->form_validation->set_rules('salaryCategoryID', 'Salary Category', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->saveOTCat());
        }
    }

    public function editOTCat()
    {
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('masterCat', 'Mater Category', 'required');
        $this->form_validation->set_rules('salaryCategoryID', 'Salary Category', 'required');
        $this->form_validation->set_rules('editID', 'ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->editOTCat());
        }
    }

    public function deleteOTCat()
    {
        $this->form_validation->set_rules('hiddenID', 'Category ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->deleteOTCat());
        }
    }
    /*End of Over time*/

    /*Start of Over time Group Master*/
    public function fetch_OTGroupMaster()
    {
        $details = '<div align="right">';
        //$details .= '<i rel="tooltip" class="fa fa-sitemap" aria-hidden="true" onclick="setupDetails(this)" title="Setup"></i>&nbsp;&nbsp; | &nbsp;&nbsp;';
        $details .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" onclick="setupDetails(this)" style="color:#3c8dbc;"></span>&nbsp;&nbsp; | &nbsp;&nbsp;';
        $details .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash traceIcon" onclick="delete_OTCat(this)" style="color:#d15b47;"></span>';
        $details .= '</div>';

        $this->datatables->select('groupID, description')
            ->from('srp_erp_pay_overtimegroupmaster ')
            ->where('companyID', current_companyID())
            ->add_column('edit', $details);


        echo $this->datatables->generate();
    }

    public function save_OTGroupMaster()
    {
        $this->form_validation->set_rules('description', 'Description', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_OTGroupMaster());
        }
    }

    public function edit_OTGroupMaster()
    {
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('editID', 'ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->edit_OTGroupMaster());
        }
    }

    public function delete_OTGroupMaster()
    {
        $this->form_validation->set_rules('catID', 'Category ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->delete_OTGroupMaster());
        }
    }

    public function save_OTGroupDet()
    {

        if (!empty($this->input->post('OT_ID'))) {
            //$this->form_validation->set_rules('OT_ID[]', 'OT Category', 'required');
            //$this->form_validation->set_rules('glCode[]', 'GL Code', 'required');
            $this->form_validation->set_rules('formulaOriginal[]', 'Formula', 'callback_validateFormula');
        }

        $this->form_validation->set_rules('groupID', 'Group ID', 'required');
        $this->form_validation->set_rules('description', 'Master Description', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_OTGroupDet());
        }
    }

    public function delete_OTGroupDetail()
    {
        $this->form_validation->set_rules('groupDet_ID', 'Category ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->delete_OTGroupDetail());
        }
    }

    function validateFormula()
    {
        $isAllSet = true;
        $unsetDesc = array();
        $formulaOriginal = $this->input->post('formulaOriginal');
        $OTDescription = $this->input->post('OTDescription');

        foreach ($formulaOriginal as $key => $row) {
            if (empty($row)) {
                $unsetDesc[] = $OTDescription[$key];
                $isAllSet = false;
            }
        }

        if ($isAllSet == false) {
            $descriptions = implode("</br>", $unsetDesc);
            $this->form_validation->set_message('validateFormula', 'Set the formula for </br>' . $descriptions);
            return false;
        } else {
            return true;
        }
    }

    /*End of Over time Group Master*/

    /********************End of employee master**************/

    /***** HR document upload****/

    function hr_document_save()
    {
        $this->form_validation->set_rules('documentName', 'Document name', 'trim|required');
        //$this->form_validation->set_rules('doc_file', 'file', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $companyID = current_companyID();
            $documentName = $this->input->post('documentName');

            $existingDataID = $this->db->select('id')->from('srp_erp_hrdocuments')
                ->where(['companyID' => $companyID, 'documentDescription' => $documentName])
                ->get()->row('id');

            if (!empty($existingDataID)) {
                die(json_encode(['e', 'This description is already exist']));
            }


            $path = UPLOAD_PATH_POS . 'documents/hr_documents/'; // imagePath();
            $fileName = $companyID . $documentName;
            $fileName = str_replace(' ', '', strtolower($fileName)) . '_' . time();
            $config['upload_path'] = $path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $config['max_size'] = '500000';
            $config['file_name'] = $fileName;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload("doc_file")) {
                die(json_encode(['e', 'Upload failed ' . $this->upload->display_errors(), $config]));
            } else {


                $data = array(
                    'documentDescription' => $documentName,
                    'documentFile' => $this->upload->data('file_name'),
                    'companyID' => $companyID,
                    'createdPCID' => current_pc(),
                    'createdUserGroup' => current_user_group(),
                    'createdUserID' => current_userID(),
                    'createdUserName' => current_employee(),
                    'createdDateTime' => current_date()
                );

                $this->db->insert('srp_erp_hrdocuments', $data);


                if ($this->db->affected_rows() > 0) {
                    echo json_encode(['s', 'Document successfully uploaded']);
                } else {
                    echo json_encode(['e', 'Error in document upload']);
                }

            }
        }
    }

    function edit_hrDocument()
    {
        $this->form_validation->set_rules('documentName', 'Document name', 'trim|required');
        $this->form_validation->set_rules('hidden-id', 'id', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $companyID = current_companyID();
            $documentName = $this->input->post('documentName');
            $id = $this->input->post('hidden-id');

            $existingDataID = $this->db->select('id')->from('srp_erp_hrdocuments')
                ->where(['companyID' => $companyID, 'documentDescription' => $documentName])
                ->get()->row('id');

            if (!empty($existingDataID) && $existingDataID != $id) {
                die(json_encode(['e', 'This description is already exist']));
            }


            $updateData = array(
                'documentDescription' => $documentName,
                'modifiedPCID' => current_pc(),
                'modifiedUserID' => current_userID(),
                'modifiedUserName' => current_employee(),
                'modifiedDateTime' => current_date()
            );

            $this->db->trans_start();

            $this->db->where(['id' => $id, 'companyID' => $companyID])->update('srp_erp_hrdocuments', $updateData);


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in document update']);
            } else {
                $this->db->trans_commit();
                echo json_encode(['s', 'Document successfully updated']);
            }

        }
    }

    function delete_hrDocument()
    {

        $this->form_validation->set_rules('hidden-id', 'id', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $companyID = current_companyID();
            $id = $this->input->post('hidden-id');


            $this->db->trans_start();
            $this->db->where(['id' => $id, 'companyID' => $companyID])->delete('srp_erp_hrdocuments');


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in document delete process']);
            } else {
                $this->db->trans_commit();
                echo json_encode(['s', 'Document successfully deleted']);
            }

        }
    }

    public function employeeMasterFilter()
    {
        echo json_encode(employeePagination());
        //echo json_encode( load_employee_data() );
        /*$empList = load_employee_data();
        employeeListCreate($empList);*/
        //echo '<pre>'; print_r($empList); echo '</pre>';
    }

    public function getEmployees()
    {
        echo json_encode($this->Employee_model->getEmployees());
    }

    public function getEmployeesDataTable()
    {
        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $entryDate = $this->input->get('entryDate');
        $segmentID = $this->input->post('segmentID');
        $currencyFilter = $this->input->post('currencyFilter');
        $currencyID = $this->input->post('currencyID');
        $isOT_addition = $this->input->post('isOT_addition');
        $selectStr = '';

        if ($entryDate != 'Not_monthly_add_deductions') {
            $entryDate = input_format_date($entryDate, $date_format_policy);

            $isNonPayroll = $this->input->post('isNonPayroll');
            $salaryDeclarationTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_pay_salarydeclartion' : 'srp_erp_pay_salarydeclartion';
            $headerDetailTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollheaderdetails' : 'srp_erp_payrollheaderdetails';
            $payrollMaster = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';
            $selectStr = ', segTB.segmentCode AS segTBCode, IFNULL(accTb.groupID, 0) AS accGroupID ';
        }

        if ($isOT_addition == 1) {
            $selectStr = ', OT_empID, OT_emp.*';
        }

        $this->datatables->select('EIdNo, ECode, Ename2 AS empName, currencyID, CurrencyCode, DecimalPlaces' . $selectStr);
        $this->datatables->from('srp_employeesdetails AS empTB');
        $this->datatables->join('srp_designation', 'empTB.EmpDesignationId = srp_designation.DesignationID');
        $this->datatables->join('srp_erp_currencymaster AS cur', 'cur.currencyID = empTB.payCurrencyID');
        $this->datatables->join('srp_erp_segment AS segTB', 'segTB.segmentID = empTB.segmentID');

        $isGroupAccess = getPolicyValues('PAC', 'All');
        if($isGroupAccess == 1){
            $currentEmp = current_userID();
            $this->datatables->join("(
                                        SELECT empTB.groupID, employeeID FROM srp_erp_payrollgroupemployees AS empTB
                                        JOIN srp_erp_payrollgroupincharge AS inCharge ON inCharge.groupID=empTB.groupID
                                        WHERE empTB.companyID={$companyID} AND inCharge.companyID={$companyID} AND empID={$currentEmp}
                                    ) AS accTb", 'accTb.employeeID=EIdNo');
        }
        else{
            $this->datatables->join("srp_erp_payrollgroupemployees AS accTb", 'accTb.employeeID=EIdNo', 'left');
        }

        if ($entryDate != 'Not_monthly_add_deductions') {
            $this->datatables->join($salaryDeclarationTB . ' AS salaryDec', 'empTB.EIdNo = salaryDec.employeeNo');
        }

        $this->datatables->add_column('addBtn', '$1', 'addBtn()');
        $this->datatables->where('empTB.Erp_companyID', $companyID);
        $this->datatables->where('empTB.isPayrollEmployee', 1);


        if ($entryDate != 'Not_monthly_add_deductions') {
            $payYear = date('Y', strtotime($entryDate));
            $payMonth = date('m', strtotime($entryDate));
            $entryDateLast = date('Y-m-t', strtotime($entryDate));

            if ($isOT_addition == 1) {
                $minDate = date('Y-m-01', strtotime($entryDate));
                $otGroupID = $this->input->post('otGroupID');
                $otGroup_str = (!empty($otGroupID)) ? ' AND grpDet.otGroupID IN (' . $otGroupID . ') ' : '';

                $this->datatables->join("(SELECT empID AS OT_empID, grpDet.*, otGroupDescription FROM srp_erp_ot_groups AS grpMaster
                                          JOIN srp_erp_ot_groupemployees AS grpEmp ON grpMaster.otGroupID = grpEmp.otGroupID
                                          AND grpEmp.companyID={$companyID}
                                          JOIN (
                                          SELECT otGroupID,
                                              MAX(CASE WHEN systemInputID = 1 THEN hourlyRate END) rateInt,
                                              MAX(CASE WHEN systemInputID = 2 THEN hourlyRate END) rateLocalLay,
                                              MAX(CASE WHEN systemInputID = 3 THEN hourlyRate END) rateIntLay,
                                              MAX(CASE WHEN systemInputID = 4 THEN slabMasterID END) slabID
                                          FROM srp_erp_ot_groupdetail WHERE companyID={$companyID} GROUP BY otGroupID
                                          ) AS grpDet ON grpDet.otGroupID = grpMaster.otGroupID
                                          WHERE grpMaster.companyID={$companyID} AND CurrencyID={$currencyID} {$otGroup_str}
                                          AND empID NOT IN (
                                                SELECT empID FROM srp_erp_ot_monthlyadditionsmaster AS addMaster
                                                JOIN srp_erp_ot_monthlyadditiondetail AS addDetail
                                                ON addDetail.monthlyAdditionsMasterID = addMaster.monthlyAdditionsMasterID AND addDetail.companyID={$companyID}
                                                WHERE addMaster.companyID={$companyID} AND dateMA BETWEEN '{$minDate}' AND '{$entryDateLast}'
                                          ) ) AS OT_emp",
                    'OT_emp.OT_empID = empTB.EIdNo');
            }


            $this->datatables->join("(SELECT EIdNo AS empID, dischargedDate,
                                      IF( isDischarged != 1, 0,
                                         CASE
                                            WHEN '{$entryDateLast}' <= DATE_FORMAT(dischargedDate, '%Y-%m-01') THEN 0
                                            WHEN '{$entryDateLast}' > DATE_FORMAT(dischargedDate, '%Y-%m-01') THEN 1
                                         END
                                      )AS isDischargedStatus FROM srp_employeesdetails WHERE Erp_companyID={$companyID}) AS dischargedStatusTB",
                'dischargedStatusTB.empID = empTB.EIdNo');
            $this->datatables->where('salaryDec.confirmedYN', 1);
            $this->datatables->where('salaryDec.payDate <=', $entryDateLast);
            $this->datatables->where('isDischargedStatus != 1');

            $this->datatables->where('EIdNo NOT IN (
                        SELECT  empID FROM ' . $payrollMaster . ' AS payMaster
                        JOIN ' . $headerDetailTB . ' AS payDet ON payDet.payrollMasterID = payMaster.payrollMasterID AND payDet.companyID=' . $companyID . '
                        WHERE payMaster.companyID = ' . $companyID . ' AND payrollYear=' . $payYear . ' AND payrollMonth=' . $payMonth . '
                  ) ');

            if (!empty($segmentID)) {
                $this->datatables->where('segTB.segmentID IN (' . $segmentID . ')');
            }

            if (!empty($currencyFilter)) {
                $this->datatables->where('currencyID IN (' . $currencyFilter . ')');
            }

            $this->datatables->group_by('salaryDec.employeeNo');
        }


        echo $this->datatables->generate();
    }

    function getDescriptionOfMonthlyAD(){
        $dateDesc = $this->input->post('dateDesc');
        $typeMonthly = $this->input->post('typeMonthly');

        $date_format_policy = date_format_policy();
        $dateDesc = input_format_date($dateDesc, $date_format_policy);
        $data = ($typeMonthly == 'MA')? 'Monthly Addition for ' : 'Monthly Deduction for ';
        $data .= date('M Y', strtotime($dateDesc));
        echo json_encode($data);
    }

    public function loadDetail_table()
    {
        $masterID = $this->input->post('masterID');
        $type = $this->input->post('type_m');
        $companyID = current_companyID();
        $data['masterData'] = $this->Employee_model->edit_monthAddition($type, $masterID);

        if ($type == 'MA') {
            $tableName = 'srp_erp_pay_monthlyadditiondetail';
            $masterColumn = 'monthlyAdditionsMasterID';
        } elseif ($type == 'MD') {
            $tableName = 'srp_erp_pay_monthlydeductiondetail';
            $masterColumn = 'monthlyDeductionMasterID';
        }


        $str = '';
        $isGroupAccess = getPolicyValues('PAC', 'All');
        $data['isGroupAccess'] = $isGroupAccess;
        if($isGroupAccess == 1){
            $totalEntries = $this->db->query("SELECT COUNT($masterColumn) AS totalEntries
                                     FROM {$tableName} AS detailTB
                                     JOIN srp_employeesdetails AS empTB ON detailTB.empID=empTB.EIdNo  AND Erp_companyID={$companyID}
                                     LEFT JOIN (
                                          SELECT monthlyDeclarationID FROM srp_erp_pay_monthlydeclarationstypes WHERE companyID={$companyID}
                                     ) AS decType ON decType.monthlyDeclarationID=detailTB.declarationID
                                     LEFT JOIN (
                                        SELECT GLAutoID, GLSecondaryCode FROM srp_erp_chartofaccounts WHERE companyID={$companyID}
                                     )AS chartAcc ON chartAcc.GLAutoID=detailTB.GLCode
                                     WHERE {$masterColumn} = {$masterID} AND detailTB.companyID = {$companyID} ORDER BY ECode ASC")->row('totalEntries');
            $data['totalEntries'] = $totalEntries;
            $currentEmp = current_userID();
            $str = "JOIN (
                        SELECT groupID FROM srp_erp_payrollgroupincharge
                        WHERE companyID={$companyID} AND empID={$currentEmp}
                    ) AS accTb ON accTb.groupID = detailTB.accessGroupID";
        }

        $details = $this->db->query("SELECT detailTB.*, EIdNo, ECode, Ename2 AS empName,
                                     IFNULL(declarationID, 0) AS declarationID, IFNULL(GLSecondaryCode, 0) AS GLSecondaryCode
                                     FROM {$tableName} AS detailTB
                                     JOIN srp_employeesdetails AS empTB ON detailTB.empID=empTB.EIdNo  AND Erp_companyID={$companyID}
                                     {$str}
                                     LEFT JOIN (
                                          SELECT monthlyDeclarationID FROM srp_erp_pay_monthlydeclarationstypes WHERE companyID={$companyID}
                                     ) AS decType ON decType.monthlyDeclarationID=detailTB.declarationID
                                     LEFT JOIN (
                                        SELECT GLAutoID, GLSecondaryCode FROM srp_erp_chartofaccounts WHERE companyID={$companyID}
                                     )AS chartAcc ON chartAcc.GLAutoID=detailTB.GLCode
                                     WHERE {$masterColumn} = {$masterID} AND detailTB.companyID = {$companyID} ORDER BY ECode ASC")->result_array();
        $data['details'] = $details;
        $this->load->view('system/hrm/ajax/monthly-add-deduction-view', $data);

    }

    public function salaryDeclaration()
    {
        $empID = $this->input->post('empID');
        $empDet = $this->Employee_model->employee_details($empID);
        $data['dPlaces'] = fetch_currency_desimal($empDet['CurrencyCode']);
        $data['salaryDet'] = $this->Employee_model->loadEmpDeclarations($empID);
        $data['salaryDetNon'] = $this->Employee_model->loadEmpDeclarations_nonPayroll($empID);

        $this->load->view('system/hrm/ajax/load_empSalaryDeclarations', $data);
    }

    public function search()
    {
        $keyword = $this->input->post('keyword');

        echo json_encode($this->Employee_model->search($keyword));
    }

    public function loadEmpDeclarations()
    {
        $empID = $this->input->post('empID');
        $declaration = $this->Employee_model->loadEmpDeclarations($empID);
        echo json_encode($declaration);
    }

    public function deleteSalaryDec()
    {
        $deleteID = $this->input->post('deleteID');
        echo json_encode($this->Employee_model->deleteSalaryDec($deleteID));
    }

    public function saveBankAccount()
    {

        $isNewBank = $this->input->post('isNewBank');
        $isNewBranch = $this->input->post('isNewBranch');

        $this->form_validation->set_rules('accHolder', 'Account Holder Name', 'trim|required');
        $this->form_validation->set_rules('bank_no', 'Bank Account Number', 'trim|required');
        $this->form_validation->set_rules('salPerc', 'Salary Transfer %', 'trim|required');


        if ($isNewBank == 1) {
            $this->form_validation->set_rules('newBank', 'Bank', 'trim|required');
        } else {
            $this->form_validation->set_rules('bank_id', 'Bank', 'trim|required');
        }

        if ($isNewBranch == 1) {
            $this->form_validation->set_rules('newBranch', 'Bank Branch', 'trim|required');
        } else {
            $this->form_validation->set_rules('branch_id', 'Bank Branch', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $accHolder = $this->input->post('accHolder');
            $bank_id = ($isNewBank == 1) ? $this->input->post('newBank') : $this->input->post('bank_id');
            $br_name = ($isNewBranch == 1) ? $this->input->post('newBranch') : $this->input->post('branch_id');
            $bank_no = $this->input->post('bank_no');
            $swiftCode = $this->input->post('swiftCode');
            $salPerc = $this->input->post('salPerc');
            $empID = $this->input->post('save_accountEmpID');

            $data = array(
                'employeeNo' => $empID,
                'bankID' => $bank_id,
                'isActive' => 1,
                'accountNo' => $bank_no,
                'accountHolderName' => $accHolder,
                'branchID' => $br_name,
                'swiftCode' => $swiftCode,
                'toBankPercentage' => $salPerc,
                'companyID' => $this->common_data['company_data']['company_id'],
                'companyCode' => $this->common_data['company_data']['company_code'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdDateTime' => current_date()
            );

            if ($isNewBank == 1) {

                $bankDet = $this->Employee_model->create_bank($bank_id);

                if ($bankDet[0] == 's') {
                    $newBankID = $bankDet[1];
                    $data['bankID'] = $newBankID;
                    $this->create_bankBranchWithSave($data, $newBankID);
                } else {
                    echo json_encode($bankDet);
                }

            } else if ($isNewBranch == 1) {
                $this->create_bankBranchWithSave($data);
            } else {
                echo json_encode($this->Employee_model->saveBankAccount($data));
            }
        }

    }

    public function create_bankBranchWithSave($data, $newBankID = null)
    {
        $newBranch = $this->input->post('newBranch');
        $branchDet = $this->Employee_model->create_bankBranch($data['bankID'], $newBranch);

        if ($branchDet[0] == 's') {
            $data['branchID'] = $branchDet[1];
            if ($newBankID != null) {
                $returnData = $this->Employee_model->saveBankAccount($data);
                $returnData[2] = $newBankID;
                echo json_encode($returnData);
            } else {
                echo json_encode($this->Employee_model->saveBankAccount($data));
            }

        } else {
            echo json_encode($branchDet);
        }
    }

    public function updateBankAccount()
    {

        $isNewBank = $this->input->post('isNewBank');
        $isNewBranch = $this->input->post('isNewBranch');

        $this->form_validation->set_rules('accHolder', 'Account Holder Name', 'trim|required');
        $this->form_validation->set_rules('bank_no', 'Bank Account Number', 'trim|required');
        $this->form_validation->set_rules('salPerc', 'Salary Transfer %', 'trim|required');
        $this->form_validation->set_rules('update_accountID', 'BAnk Account ID', 'trim|required');


        if ($isNewBank == 1) {
            $this->form_validation->set_rules('newBank', 'Bank', 'trim|required');
        } else {
            $this->form_validation->set_rules('bank_id', 'Bank', 'trim|required');
        }

        if ($isNewBranch == 1) {
            $this->form_validation->set_rules('newBranch', 'Bank Branch', 'trim|required');
        } else {
            $this->form_validation->set_rules('branch_id', 'Bank Branch', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $id = $this->input->post('update_accountID');
            $accHolder = $this->input->post('accHolder');
            $bank_id = ($isNewBank == 1) ? $this->input->post('newBank') : $this->input->post('bank_id');
            $br_name = ($isNewBranch == 1) ? $this->input->post('newBranch') : $this->input->post('branch_id');
            $bank_no = $this->input->post('bank_no');
            $salPerc = $this->input->post('salPerc');
            $accStatus = $this->input->post('accStatus');
            $swiftCode = $this->input->post('swiftCode');


            $data = array(
                'bankID' => $bank_id,
                'branchID' => $br_name,
                'accountNo' => $bank_no,
                'accountHolderName' => $accHolder,
                'toBankPercentage' => $salPerc,
                'swiftCode' => $swiftCode,
                'isActive' => $accStatus,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => current_date()
            );


            if ($isNewBank == 1) {

                $bankDet = $this->Employee_model->create_bank($bank_id);

                if ($bankDet[0] == 's') {
                    $newBankID = $bankDet[1];
                    $data['bankID'] = $newBankID;
                    $this->create_bankBranchWithUpdate($data, $id, $newBankID);
                } else {
                    echo json_encode($bankDet);
                }

            } else if ($isNewBranch == 1) {
                $this->create_bankBranchWithUpdate($data, $id);
            } else {
                echo json_encode($this->Employee_model->updateBankAccount($data, $id));
            }
        }

    }

    public function create_bankBranchWithUpdate($data, $id, $newBankID = null)
    {
        $newBranch = $this->input->post('newBranch');
        $branchDet = $this->Employee_model->create_bankBranch($data['bankID'], $newBranch);

        if ($branchDet[0] == 's') {
            $data['branchID'] = $branchDet[1];
            if ($newBankID != null) {
                $returnData = $this->Employee_model->updateBankAccount($data, $id);
                $returnData[2] = $newBankID;
                echo json_encode($returnData);
            } else {
                echo json_encode($this->Employee_model->updateBankAccount($data, $id));
            }

        } else {
            echo json_encode($branchDet);
        }
    }

    public function inactiveBankAccount()
    {
        $deleteID = $this->input->post('deleteID');

        $data = array(
            'isActive' => 0,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => current_date()
        );

        echo json_encode($this->Employee_model->deleteBankAccount($data, $deleteID));
    }

    public function loadEmpBankAccount()
    {
        $empID = $this->input->post('empID');

        $empAccDet = $this->Employee_model->loadEmpBankAccount($empID);

        echo json_encode($empAccDet);
    }

    public function searchInEmpLoan()
    {
        $keyword = $this->input->get('keyword');
        $emp = $this->Employee_model->searchInEmpLoan($keyword);

        if (!empty($emp)) {
            echo json_encode($emp);
        } else {
            $noData[0] = array(
                'DesDescription' => '',
                'EIdNo' => '',
                'Ename1' => '',
                'Ename2' => '',
                'Ename3' => '',
                'ECode' => '',
                'Match' => 'No records',
            );
            echo json_encode($noData);
        }
    }

    public function save_monthAddition()
    {
        $this->form_validation->set_rules('dateDesc', 'Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $unProcessedEmployees = $this->payrollNotProcessedEmployees();

            if ($unProcessedEmployees[0] == 's') {
                echo json_encode($this->Employee_model->save_monthAddition('MA'));
            } else {
                echo json_encode($unProcessedEmployees);
            }
        }
    }

    function getCountOfSalaryDeclaredEmployees($isNonPayroll, $payDate)
    {
        $companyID = current_companyID();
        $salaryDeclarationTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_pay_salarydeclartion' : 'srp_erp_pay_salarydeclartion';
        $empCount = $this->db->query("SELECT COUNT(id) countEmp FROM {$salaryDeclarationTB} WHERE companyID={$companyID}
                                      AND payDate <='{$payDate}'")->row('countEmp');
        return $empCount;
    }

    function payrollNotProcessedEmployees()
    {
        $companyID = current_companyID();
        $isNonPayroll = $this->input->post('payrollType');
        $date_format_policy = date_format_policy();
        $payDate = input_format_date($this->input->post('dateDesc'), $date_format_policy);
        $payYear = date('Y', strtotime($payDate));
        $payMonth = date('m', strtotime($payDate));


        $salaryDeclarationTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_pay_salarydeclartion' : 'srp_erp_pay_salarydeclartion';
        $headerDetailTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollheaderdetails' : 'srp_erp_payrollheaderdetails';
        $payrollMaster = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';

        $isSalaryDeclared = $this->getCountOfSalaryDeclaredEmployees($isNonPayroll, $payDate);
        if ($isSalaryDeclared == 0) {
            return ['e', 'Salary is not declared for any employee on or before this date.'];
        }

        $processedEmp = $this->db->query("SELECT EIdNo, ECode, Ename2 AS empName
                                          FROM srp_employeesdetails AS empTB
                                          JOIN (
                                              SELECT employeeNo FROM {$salaryDeclarationTB} WHERE companyID={$companyID}
                                              AND payDate<='{$payDate}' GROUP BY employeeNo
                                          ) AS declarationTB ON declarationTB.employeeNo=empTB.EIdNo
                                          JOIN (
                                              SELECT EIdNo AS empID, dischargedDate,
                                              IF( isDischarged != 1, 0,
                                                    CASE
                                                       WHEN DATE_FORMAT('{$payDate}', '%Y-%m-01') <= DATE_FORMAT(dischargedDate, '%Y-%m-01') THEN 0
                                                       WHEN DATE_FORMAT('{$payDate}', '%Y-%m-01') > DATE_FORMAT(dischargedDate, '%Y-%m-01') THEN 1
                                                    END
                                              ) AS isDischargedStatus
                                              FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                          ) AS dischargedStatusTB ON dischargedStatusTB.empID = empTB.EIdNo
                                          WHERE empTB.Erp_companyID={$companyID} AND empConfirmedYN=1 AND empTB.isPayrollEmployee = 1 AND isDischargedStatus != 1
                                          AND  EIdNo NOT IN (
                                              SELECT  empID FROM {$payrollMaster} AS payMaster
                                              JOIN {$headerDetailTB} AS payDet ON payDet.payrollMasterID = payMaster.payrollMasterID AND payDet.companyID={$companyID}
                                              WHERE payMaster.companyID={$companyID} AND payrollYear={$payYear} AND payrollMonth={$payMonth}
                                          )")->result_array();

        if (count($processedEmp) == 0) {
            return ['e', 'Payroll has been processed for all the employees on this month.'];
        }

        return ['s'];
    }

    public function save_monthDeduction()
    {
        $this->form_validation->set_rules('dateDesc', 'Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            /*$date_format_policy = date_format_policy();
            $dtDec = $this->input->post('dateDesc');
            $dateDesc = input_format_date($dtDec, $date_format_policy);
            $payrollType = ($this->input->post('payrollType') == 'Y') ? 2 : 1;

            $this->load->helper('template_paySheet_helper');
            $isPayrollProcessed = isPayrollProcessed($dateDesc, $payrollType);

            if ($isPayrollProcessed['status'] == 'N') {
                echo json_encode($this->Employee_model->save_monthAddition('MD'));
            } else {
                $greaterThanDate = date('Y - F', strtotime($isPayrollProcessed['year'] . '-' . $isPayrollProcessed['month'] . '-01'));
                echo json_encode(array('e', 'Monthly addition date should be  <p> greater than [ ' . $greaterThanDate . ' ] '));
            }*/

            $unProcessedEmployees = $this->payrollNotProcessedEmployees();

            if ($unProcessedEmployees[0] == 's') {
                echo json_encode($this->Employee_model->save_monthAddition('MD'));
            } else {
                echo json_encode($unProcessedEmployees);
            }
        }
    }

    public function load_monthlyAdditionMaster_table()
    {
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $isNonPayroll = $this->input->post('isNonPayroll');
        $isGroupAccess = getPolicyValues('PAC', 'All');

        $this->datatables->select('monthlyAdditionsMasterID AS masterID, monthlyAdditionsCode, description, DATE_FORMAT(dateMA,\'' . $convertFormat . '\') AS dateMA,
                                confirmedYN, approvedYN, isProcessed, isNonPayroll', false)
            ->from('srp_erp_pay_monthlyadditionsmaster')
            ->add_column('status', '$1', 'confirm(confirmedYN)')
            ->add_column('action', '$1', 'monthlyDeclarationsAction(masterID, confirmedYN, approvedYN, isProcessed, "A", monthlyAdditionsCode, isNonPayroll)')
            ->where('companyID', $companyID)
            ->where('isNonPayroll', $isNonPayroll);

        if($isGroupAccess == 1){
            $currentEmp = current_userID();
            // Usage of UNION in this sub query
            // to get the declaration master record that are not contain any record in detail table record
            // which means we can not get the access rights with out a employee in detail table

            $this->datatables->join("(SELECT addID FROM srp_erp_payrollgroupincharge AS inCharge
                                  JOIN (
                                        SELECT monthlyAdditionsMasterID AS addID, accessGroupID
                                        FROM srp_erp_pay_monthlyadditiondetail
                                        WHERE companyID={$companyID} AND accessGroupID IS NOT NULL
                                        GROUP BY monthlyAdditionsMasterID, accessGroupID
                                  ) AS declrationTB ON inCharge.groupID=declrationTB.accessGroupID
                                  WHERE companyID={$companyID} AND empID={$currentEmp}
                                  GROUP BY addID
                                  UNION
                                      SELECT t1.monthlyAdditionsMasterID
                                      FROM srp_erp_pay_monthlyadditionsmaster AS t1
                                      LEFT JOIN srp_erp_pay_monthlyadditiondetail AS t2
                                      ON t2.monthlyAdditionsMasterID=t1.monthlyAdditionsMasterID
                                      WHERE t1.companyID={$companyID} AND t2.monthlyAdditionsMasterID IS NULL
                                      GROUP BY t1.monthlyAdditionsMasterID
                                  ) AS accTB", 'srp_erp_pay_monthlyadditionsmaster.monthlyAdditionsMasterID = accTB.addID');

        }
        echo $this->datatables->generate();
    }

    public function load_monthlyDeductionMaster_table()
    {
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $isNonPayroll = $this->input->post('isNonPayroll');
        $isGroupAccess = getPolicyValues('PAC', 'All');

        $this->datatables->select('monthlyDeductionMasterID AS masterID, monthlyDeductionCode, description,DATE_FORMAT(dateMD,\'' . $convertFormat . '\') AS dateMD,
                                confirmedYN, isProcessed, isNonPayroll', false)
            ->from('srp_erp_pay_monthlydeductionmaster')
            ->add_column('status', '$1', 'confirm(confirmedYN)')
            ->add_column('action', '$1', 'monthlyDeclarationsAction(masterID, confirmedYN, approvedYN, isProcessed, "D", monthlyDeductionCode, isNonPayroll)')
            ->where('companyID', current_companyID())
            ->where('isNonPayroll', $isNonPayroll);
        if($isGroupAccess == 1){
            $currentEmp = current_userID();
            // Usage of UNION in this sub query
            // to get the declaration master record that are not contain any record in detail table record
            // which means we can not get the access rights with out a employee in detail table

            $this->datatables->join("(SELECT deductionID FROM srp_erp_payrollgroupincharge AS inCharge
                                  JOIN (
                                        SELECT monthlyDeductionMasterID AS deductionID, accessGroupID
                                        FROM srp_erp_pay_monthlydeductiondetail
                                        WHERE companyID={$companyID} AND accessGroupID IS NOT NULL
                                        GROUP BY monthlyDeductionMasterID, accessGroupID
                                  ) AS declrationTB ON inCharge.groupID=declrationTB.accessGroupID
                                  WHERE companyID={$companyID} AND empID={$currentEmp}
                                  GROUP BY deductionID
                                  UNION
                                      SELECT t1.monthlyDeductionMasterID
                                      FROM srp_erp_pay_monthlydeductionmaster AS t1
                                      LEFT JOIN srp_erp_pay_monthlydeductiondetail AS t2
                                      ON t2.monthlyDeductionMasterID=t1.monthlyDeductionMasterID
                                      WHERE t1.companyID={$companyID} AND t2.monthlyDeductionMasterID IS NULL
                                      GROUP BY t1.monthlyDeductionMasterID
                                  ) AS accTB", 'srp_erp_pay_monthlydeductionmaster.monthlyDeductionMasterID = accTB.deductionID');

        }

        echo $this->datatables->generate();
    }

    public function save_empMonthlyAddition()
    {

        $this->form_validation->set_rules('dateDesc', 'Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $date_format_policy = date_format_policy();
            $masterID = $this->input->post('updateID');
            $empArr = $this->input->post('empHiddenID');
            $dtDsc = $this->input->post('dateDesc');
            $dateDesc = input_format_date($dtDsc, $date_format_policy);
            $payYear = date('Y', strtotime($dateDesc));
            $payMonth = date('m', strtotime($dateDesc));
            $isPayrollProcessed = null;
            $masterData = $this->Employee_model->edit_monthAddition('MA', $masterID);

            /*$this->load->helper('template_paySheet_helper');
            $isPayrollProcessed = isPayrollProcessed($dateDesc, $payrollType);*/

            $isSalaryDeclared = $this->getCountOfSalaryDeclaredEmployees($masterData['isNonPayroll'], $dateDesc);

            if ($isSalaryDeclared == 0) {
                die(json_encode(['e', 'Salary is not declared for any employee on or before this date.']));
            }

            if (!empty($empArr)) {
                $empArr = join(',', $empArr);
                $isPayrollProcessed = isPayrollProcessedForEmpGroup($empArr, $payYear, $payMonth, $masterData['isNonPayroll']);
            }

            if (empty($isPayrollProcessed)) {
                echo json_encode($this->Employee_model->save_empMonthlyAddition('MA'));
            } else {
                $employeesStr = implode('<br/>', array_column($isPayrollProcessed, 'empData'));
                $yearMonth = date('Y - M', strtotime($dtDsc));
                echo json_encode(array('e', 'Payroll already processed on selected <br/> month (' . $yearMonth . ') for following employees <br/>' . $employeesStr));
            }
        }
    }

    public function save_empMonthlyDeduction()
    {
        $this->form_validation->set_rules('dateDesc', 'Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $date_format_policy = date_format_policy();
            $masterID = $this->input->post('updateID');
            $empArr = $this->input->post('empHiddenID');
            $dtDsc = $this->input->post('dateDesc');
            $dateDesc = input_format_date($dtDsc, $date_format_policy);
            $payYear = date('Y', strtotime($dateDesc));
            $payMonth = date('m', strtotime($dateDesc));

            $masterData = $this->Employee_model->edit_monthAddition('MD', $masterID);
            /*$payrollType = ($masterData['isNonPayroll'] == 'Y') ? 2 : 1;
            $this->load->helper('template_paySheet_helper');
            $isPayrollProcessed = isPayrollProcessed($dateDesc, $payrollType);

            if ($isPayrollProcessed['status'] == 'N') {
                echo json_encode($this->Employee_model->save_empMonthlyAddition('MD'));
            } else {
                $greaterThanDate = date('Y - F', strtotime($isPayrollProcessed['year'] . '-' . $isPayrollProcessed['month'] . '-01'));
                echo json_encode(array('e', 'Monthly deduction date should be  <p> greater than [ ' . $greaterThanDate . ' ] '));
            }*/

            $isSalaryDeclared = $this->getCountOfSalaryDeclaredEmployees($masterData['isNonPayroll'], $dateDesc);

            if ($isSalaryDeclared == 0) {
                die(json_encode(['e', 'Salary is not declared for any employee on or before this date.']));
            }

            if (!empty($empArr)) {
                $empArr = join(',', $empArr);
                $isPayrollProcessed = isPayrollProcessedForEmpGroup($empArr, $payYear, $payMonth, $masterData['isNonPayroll']);
            }

            if (empty($isPayrollProcessed)) {
                echo json_encode($this->Employee_model->save_empMonthlyAddition('MD'));
            } else {
                $employeesStr = implode('<br/>', array_column($isPayrollProcessed, 'empData'));
                $yearMonth = date('Y - M', strtotime($dtDsc));
                echo json_encode(array('e', 'Payroll already processed on selected <br/> month (' . $yearMonth . ') for following employees <br/>' . $employeesStr));
            }
        }
    }

    public function save_employeeAsTemp()
    {
        $this->form_validation->set_rules('masterID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('dateDesc', 'Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $monthlyAD = $this->input->post('type_m'); // MA | MD
            $empHiddenID = $this->input->post('temp_empHiddenID');
            if (count($empHiddenID) > 0) {

                $date_format_policy = date_format_policy();
                $masterID = $this->input->post('updateID');
                $dtDsc = $this->input->post('dateDesc');
                $empArr = $this->input->post('temp_empHiddenID');
                $dateDesc = input_format_date($dtDsc, $date_format_policy);
                $payYear = date('Y', strtotime($dateDesc));
                $payMonth = date('m', strtotime($dateDesc));

                $masterData = $this->Employee_model->edit_monthAddition($monthlyAD, $masterID);


                $this->load->helper('template_paySheet_helper');

                $empArr = join(',', $empArr);
                $isPayrollProcessed = isPayrollProcessedForEmpGroup($empArr, $payYear, $payMonth, $masterData['isNonPayroll']);


                if (empty($isPayrollProcessed)) {
                    if (!empty($this->input->post('empHiddenID'))) {
                        $isProcessSuccess = $this->Employee_model->save_empMonthlyAddition($monthlyAD);
                    } else {
                        $isProcessSuccess[0] = 's';
                    }

                    if ($isProcessSuccess[0] == 's') {
                        echo json_encode($this->Employee_model->save_employeeAsTemp());
                    } else {
                        echo json_encode($isProcessSuccess);
                    }

                } else {
                    $employeesStr = implode('<br/>', array_column($isPayrollProcessed, 'empData'));
                    echo json_encode(array('e', 'Payroll already processed on selected <br/> month (' . $dtDsc . ') for following employees <br/>' . $employeesStr));
                }

            } else {
                echo json_encode(array('e', 'There are no one selected to proceed'));
            }

        }
    }

    public function monthlyAddDeduction_excelUpload(){
        $masterID = $this->input->post('masterID');
        $type_m = $this->input->post('type_m');
        $docDate = $this->input->post('docDate');
        $date_format_policy = date_format_policy();
        $docDate = input_format_date($docDate, $date_format_policy);
        $lastDateOfMonth = date('Y-m-t', strtotime($docDate));;
        $year = date('Y', strtotime($docDate));
        $month = date('m', strtotime($docDate));
        $companyID = current_companyID();
        $i = 0; $m = 0;
        $current_date = current_date();


        if(empty($masterID)){
            die( json_encode(['e', 'Id field is required']) );
        }

        if ($type_m == 'MA') {
            $tableName = 'srp_erp_pay_monthlyadditiondetail';
            $masterColumn = 'monthlyAdditionsMasterID';
        } elseif ($type_m == 'MD') {
            $tableName = 'srp_erp_pay_monthlydeductiondetail';
            $masterColumn = 'monthlyDeductionMasterID';
        }

        if (isset($_FILES['excelUpload_file']['size']) && $_FILES['excelUpload_file']['size'] > 0) {
            $type = explode(".",$_FILES['excelUpload_file']['name']);
            if(strtolower(end($type)) != 'csv'){
                die( json_encode(['e', 'File type is not csv - ',$type]) );
            }

            //Get all employees in the company
            $empArr = $this->db->query("SELECT EIdNo, ECode, Ename2 FROM srp_employeesdetails WHERE Erp_companyID={$companyID}")->result_array();
            $emp_list = array_column($empArr, 'ECode');

            $filename = $_FILES["excelUpload_file"]["tmp_name"];
            $file = fopen($filename, "r");
            $dataExcel = [];
            $unMatchRecords = [];
            while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                if ($i > 0) {
                    $excelEmpCode = trim($getData[0]);
                    $amount = trim($getData[2]);
                    $amount = str_replace(',', '', $amount);
                    if(!empty($excelEmpCode) && ($amount > 0)){

                        $keys = array_keys($emp_list, $excelEmpCode);
                        $thisEmpData = array_map(function ($k) use ($empArr) {
                            return $empArr[$k];
                        }, $keys);

                        if(!empty($thisEmpData[0])){
                            $dataExcel[$m]['empID'] = $thisEmpData[0]['EIdNo'];
                            $dataExcel[$m][$masterColumn] = $masterID;
                            $dataExcel[$m]['transactionAmount'] = $amount;
                            $dataExcel[$m]['empCodeName'] = trim($excelEmpCode.' - '. trim($getData[1]));

                            $m++;
                        }else{
                            $unMatchRecords[] = ' &nbsp;&nbsp;- '.$excelEmpCode;
                        }
                    }
                }
                $i++;
            }
            fclose($file);

            if(!empty($unMatchRecords)){
                $msg = '<strong>Following Employee codes does not match with the database.</strong><br/>';
                $msg .= implode('<br/>' , $unMatchRecords);
                die( json_encode(['m', $msg]) );
            }


            if(!empty($dataExcel)){
                $alreadyPayrollProcessed = [];
                $dischargedList = [];
                $notMatchRecords = [];
                $com_currencyID = $this->common_data['company_data']['company_default_currencyID'];
                $com_currency = $this->common_data['company_data']['company_default_currency'];
                $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];
                $groupEmpBy = array_group_by($dataExcel, 'empID');

                $data = []; $k = 0; $canPull = true;
                foreach($groupEmpBy as $key=>$row){
                    $empID = $key;


                    $empData =$this->db->query("SELECT EIdNo, ECode, Ename2 AS empName, currencyID, CurrencyCode, DecimalPlaces,
                                                IF(ISNULL(empID_pay), 'N', 'Y') AS isPayrollProcessed, isDischargedStatus, groupID
                                                FROM srp_employeesdetails AS empTB
                                                JOIN srp_designation ON empTB.EmpDesignationId = srp_designation.DesignationID
                                                JOIN srp_erp_currencymaster AS cur ON cur.currencyID = empTB.payCurrencyID
                                                JOIN srp_erp_pay_salarydeclartion AS salaryDec ON empTB.EIdNo = salaryDec.employeeNo
                                                JOIN (
                                                    SELECT EIdNo AS empID, dischargedDate,
                                                    IF( isDischarged != 1, 0,
                                                         CASE
                                                             WHEN '{$lastDateOfMonth}' <= DATE_FORMAT(dischargedDate, '%Y-%m-01') THEN 0
                                                             WHEN '{$lastDateOfMonth}' > DATE_FORMAT(dischargedDate, '%Y-%m-01') THEN 1
                                                         END
                                                    )AS isDischargedStatus FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                                ) AS dischargedStatusTB ON dischargedStatusTB.empID = empTB.EIdNo
                                                LEFT JOIN (
                                                    SELECT empID AS empID_pay FROM srp_erp_payrollmaster AS payMaster
                                                    JOIN srp_erp_payrollheaderdetails AS payDet ON payDet.payrollMasterID = payMaster.payrollMasterID
                                                    WHERE payMaster.companyID = {$companyID} AND payDet.companyID={$companyID} AND payrollYear={$year}
                                                    AND payrollMonth={$month}
                                                ) AS thisPayTB ON thisPayTB.empID_pay = empTB.EIdNo
                                                LEFT JOIN(
                                                  SELECT groupID, employeeID FROM srp_erp_payrollgroupemployees
                                                  WHERE employeeID={$empID} AND companyID={$companyID}
                                                ) AS accTb ON accTb.employeeID = empTB.EIdNo
                                                WHERE empTB.Erp_companyID = '{$companyID}' AND empTB.isPayrollEmployee = 1 AND salaryDec.confirmedYN = 1
                                                AND salaryDec.payDate <= '{$lastDateOfMonth}' AND empTB.EIdNo={$empID}
                                                GROUP BY salaryDec.employeeNo")->row_array();


                    if(!empty($empData)){

                        if( $empData['isPayrollProcessed'] == 'Y'){ /*** If payroll already processed ***/
                            $alreadyPayrollProcessed[] = ' &nbsp;&nbsp;- '.$row[0]['empCodeName'];
                            $canPull = false;
                        }
                        if( $empData['isDischargedStatus'] == '1'){ /*** If employee discharged ***/
                            $dischargedList[] = ' &nbsp;&nbsp;- '.$row[0]['empCodeName'];
                            $canPull = false;
                        }

                        if($canPull == true){
                            $trCurrencyID = $empData['currencyID'];
                            $com_exchangeRateData = currency_conversionID($trCurrencyID, $com_currencyID);
                            $com_exchangeRate = $com_exchangeRateData['conversion'];

                            foreach($row as $keyEmp=>$rowEmp){
                                $data[$k]['empID'] = $empID;
                                $data[$k]['accessGroupID'] = $empData['groupID'];
                                $data[$k][$masterColumn] = $masterID;

                                $trAmount = round($rowEmp['transactionAmount'], $empData['DecimalPlaces']);
                                $localAmount = ($trAmount /$com_exchangeRate);

                                $data[$k]['transactionAmount'] = $trAmount;
                                $data[$k]['transactionCurrencyID'] = $trCurrencyID;
                                $data[$k]['transactionCurrency'] = $empData['CurrencyCode'];
                                $data[$k]['transactionExchangeRate'] = 1;
                                $data[$k]['transactionCurrencyDecimalPlaces'] = $empData['DecimalPlaces'];


                                $data[$k]['companyLocalCurrencyID'] = $com_currencyID;
                                $data[$k]['companyLocalCurrency'] = $com_currency;
                                $data[$k]['companyLocalExchangeRate'] = $com_exchangeRate;
                                $data[$k]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;
                                $data[$k]['companyLocalAmount'] = round($localAmount, $com_currDPlace);

                                $data[$k]['companyID'] = $companyID;
                                $data[$k]['companyCode'] = $this->common_data['company_data']['company_code'];
                                $data[$k]['createdPCID'] = $this->common_data['current_pc'];
                                $data[$k]['createdUserID'] = $this->common_data['current_userID'];
                                $data[$k]['createdUserName'] = $this->common_data['current_user'];
                                $data[$k]['createdUserGroup'] = $this->common_data['user_group'];
                                $data[$k]['createdDateTime'] = $current_date;

                                $k++;
                            }
                        }

                    }
                    else{
                        $canPull = false;
                        $notMatchRecords[] = ' &nbsp;&nbsp;- '.$row[0]['empCodeName'].' - '.$empID;
                    }


                }

                if($canPull == false){
                    $msg = '';
                    if(!empty($alreadyPayrollProcessed)){
                        $msg .= '<strong>Payroll already processed for following employees</strong><br/>';
                        $msg .= implode('<br/>' , $alreadyPayrollProcessed);
                    }
                    if(!empty($dischargedList)){
                        $msg .= ($msg == '')? '':'<br/>' ;
                        $msg .= '<strong>Following employees already discharged</strong><br/>';
                        $msg .= implode('<br/>' , $dischargedList);
                    }
                    if(!empty($notMatchRecords)){
                        $msg .= ($msg == '')? '':'<br/>' ;
                        //$msg .= '<strong>Following record are not match with the database</strong><br/>(Please verify the salary declaration)<br/>';
                        $msg .= '<strong>Please verify following records with salary declarations</strong><br/>';
                        $msg .= implode('<br/>' , $notMatchRecords);
                    }

                    die( json_encode(['m', $msg]) );
                }


                if(!empty($data)){

                    $this->db->trans_start();
                    $this->db->insert_batch($tableName, $data);
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        die( json_encode(['e', 'Error in process']) );
                    } else {
                        $this->db->trans_commit();
                        die( json_encode(['s', 'Successfully uploaded']) );
                    }

                }
            }
            else{
                die( json_encode(['e', 'File is empty']) );
            }

        } else {
            echo json_encode(['e', 'Please Select CSV File .']);
        }
    }

    function download_csv(){

        $segment = $this->input->post('segment');
        $segmentFilter = '';

        if(!empty($segment)){
            $segmentFilter = implode(',', $segment);
            $segmentFilter = 'AND segmentID IN ('.$segmentFilter.')';
        }
        $companyID =  current_companyID();
        $empArr = $this->db->query("SELECT EIdNo, ECode, Ename2 FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                    AND isPayrollEmployee =1 AND isDischarged=0 AND empConfirmedYN=1 {$segmentFilter}")->result_array();
        $csv_data = [
            [
                0 => 'Code',
                1 => 'Name',
                2 => 'Amount',
            ]
        ];

        foreach($empArr as $key=>$row){
            $csv_data[$key+1][1] = $row['ECode'];
            $csv_data[$key+1][2] = $row['Ename2'];
            $csv_data[$key+1][3] = '0';
        }

        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=file.csv");


        $output = fopen("php://output", "w");
        foreach ($csv_data as $row){
            fputcsv($output, $row);
        }
        fclose($output);
    }

    function download_csv_old(){

        $segment = $this->input->post('segment');
        $segmentFilter = '';

        if(!empty($segment)){
            $segmentFilter = implode(',', $segment);
            $segmentFilter = 'AND segmentID IN ('.$segmentFilter.')';
        }
        $companyID =  current_companyID();
        $empArr = $this->db->query("SELECT EIdNo, ECode, Ename2 FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                    AND isPayrollEmployee =1 AND isDischarged=0 AND empConfirmedYN=1 {$segmentFilter}")->result_array();
        $csv_data = [
            [
                0 => 'ID',
                1 => 'Code',
                2 => 'Name',
                3 => 'Amount',
            ]
        ];

        foreach($empArr as $key=>$row){
            $csv_data[$key+1][0] = $row['EIdNo'];
            $csv_data[$key+1][1] = $row['ECode'];
            $csv_data[$key+1][2] = $row['Ename2'];
            $csv_data[$key+1][3] = '0';
        }

        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=file.csv");


        $output = fopen("php://output", "w");
        foreach ($csv_data as $row){
            fputcsv($output, $row);
        }
        fclose($output);
    }

    public function monthlyAddDeduction_excelUpload_old(){
        $masterID = $this->input->post('masterID');
        $type_m = $this->input->post('type_m');
        $docDate = $this->input->post('docDate');
        $date_format_policy = date_format_policy();
        $docDate = input_format_date($docDate, $date_format_policy);
        $lastDateOfMonth = date('Y-m-t', strtotime($docDate));;
        $year = date('Y', strtotime($docDate));
        $month = date('m', strtotime($docDate));
        $companyID = current_companyID();
        $i = 0; $m = 0;
        $current_date = current_date();


        if(empty($masterID)){
            die( json_encode(['e', 'Id field is required']) );
        }

        if ($type_m == 'MA') {
            $tableName = 'srp_erp_pay_monthlyadditiondetail';
            $masterColumn = 'monthlyAdditionsMasterID';
        } elseif ($type_m == 'MD') {
            $tableName = 'srp_erp_pay_monthlydeductiondetail';
            $masterColumn = 'monthlyDeductionMasterID';
        }

        if (isset($_FILES['excelUpload_file']['size']) && $_FILES['excelUpload_file']['size'] > 0) {
            $type = explode(".",$_FILES['excelUpload_file']['name']);
            if(strtolower(end($type)) != 'csv'){
                die( json_encode(['e', 'File type is not csv - ',$type]) );
            }

            $filename = $_FILES["excelUpload_file"]["tmp_name"];
            $file = fopen($filename, "r");
            $dataExcel = [];
            while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                if ($i > 0) {
                    $excelEmpID = trim($getData[0]);
                    $amount = trim($getData[3]);
                    if(!empty($excelEmpID) && ($amount > 0)){
                        $dataExcel[$m]['empID'] = $excelEmpID;
                        $dataExcel[$m][$masterColumn] = $masterID;
                        $dataExcel[$m]['transactionAmount'] = $amount;
                        $dataExcel[$m]['empCodeName'] = trim($getData[1]).' | '.trim($getData[2]);
                        $m++;
                    }
                }
                $i++;
            }
            fclose($file);

            if(!empty($dataExcel)){
                $alreadyPayrollProcessed = [];
                $dischargedList = [];
                $notMatchRecords = [];
                $com_currencyID = $this->common_data['company_data']['company_default_currencyID'];
                $com_currency = $this->common_data['company_data']['company_default_currency'];
                $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];
                $groupEmpBy = array_group_by($dataExcel, 'empID');

                $data = []; $k = 0; $canPull = true;
                foreach($groupEmpBy as $key=>$row){
                    $empID = $key;


                    $empData =$this->db->query("SELECT EIdNo, ECode, Ename2 AS empName, currencyID, CurrencyCode, DecimalPlaces,
                                                IF(ISNULL(empID_pay), 'N', 'Y') AS isPayrollProcessed, isDischargedStatus, groupID
                                                FROM srp_employeesdetails AS empTB
                                                JOIN srp_designation ON empTB.EmpDesignationId = srp_designation.DesignationID
                                                JOIN srp_erp_currencymaster AS cur ON cur.currencyID = empTB.payCurrencyID
                                                JOIN srp_erp_pay_salarydeclartion AS salaryDec ON empTB.EIdNo = salaryDec.employeeNo
                                                JOIN (
                                                    SELECT EIdNo AS empID, dischargedDate,
                                                    IF( isDischarged != 1, 0,
                                                         CASE
                                                             WHEN '{$lastDateOfMonth}' <= DATE_FORMAT(dischargedDate, '%Y-%m-01') THEN 0
                                                             WHEN '{$lastDateOfMonth}' > DATE_FORMAT(dischargedDate, '%Y-%m-01') THEN 1
                                                         END
                                                    )AS isDischargedStatus FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                                ) AS dischargedStatusTB ON dischargedStatusTB.empID = empTB.EIdNo
                                                LEFT JOIN (
                                                    SELECT empID AS empID_pay FROM srp_erp_payrollmaster AS payMaster
                                                    JOIN srp_erp_payrollheaderdetails AS payDet ON payDet.payrollMasterID = payMaster.payrollMasterID
                                                    WHERE payMaster.companyID = {$companyID} AND payDet.companyID={$companyID} AND payrollYear={$year}
                                                    AND payrollMonth={$month}
                                                ) AS thisPayTB ON thisPayTB.empID_pay = empTB.EIdNo
                                                LEFT JOIN(
                                                  SELECT groupID, employeeID FROM srp_erp_payrollgroupemployees
                                                  WHERE employeeID={$empID} AND companyID={$companyID}
                                                ) AS accTb ON accTb.employeeID = empTB.EIdNo
                                                WHERE empTB.Erp_companyID = '{$companyID}' AND empTB.isPayrollEmployee = 1 AND salaryDec.confirmedYN = 1
                                                AND salaryDec.payDate <= '{$lastDateOfMonth}' AND empTB.EIdNo={$empID}
                                                GROUP BY salaryDec.employeeNo")->row_array();


                    if(!empty($empData)){

                        if( $empData['isPayrollProcessed'] == 'Y'){ /*** If payroll already processed ***/
                            $alreadyPayrollProcessed[] = ' &nbsp;&nbsp;- '.$row[0]['empCodeName'];
                            $canPull = false;
                        }
                        if( $empData['isDischargedStatus'] == '1'){ /*** If employee discharged ***/
                            $dischargedList[] = ' &nbsp;&nbsp;- '.$row[0]['empCodeName'];
                            $canPull = false;
                        }

                        if($canPull == true){
                            $trCurrencyID = $empData['currencyID'];
                            $com_exchangeRateData = currency_conversionID($trCurrencyID, $com_currencyID);
                            $com_exchangeRate = $com_exchangeRateData['conversion'];

                            foreach($row as $keyEmp=>$rowEmp){
                                $data[$k]['empID'] = $empID;
                                $data[$k]['accessGroupID'] = $empData['groupID'];
                                $data[$k][$masterColumn] = $masterID;

                                $trAmount = round($rowEmp['transactionAmount'], $empData['DecimalPlaces']);
                                $localAmount = ($trAmount /$com_exchangeRate);

                                $data[$k]['transactionAmount'] = $trAmount;
                                $data[$k]['transactionCurrencyID'] = $trCurrencyID;
                                $data[$k]['transactionCurrency'] = $empData['CurrencyCode'];
                                $data[$k]['transactionExchangeRate'] = 1;
                                $data[$k]['transactionCurrencyDecimalPlaces'] = $empData['DecimalPlaces'];


                                $data[$k]['companyLocalCurrencyID'] = $com_currencyID;
                                $data[$k]['companyLocalCurrency'] = $com_currency;
                                $data[$k]['companyLocalExchangeRate'] = $com_exchangeRate;
                                $data[$k]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;
                                $data[$k]['companyLocalAmount'] = round($localAmount, $com_currDPlace);

                                $data[$k]['companyID'] = $companyID;
                                $data[$k]['companyCode'] = $this->common_data['company_data']['company_code'];
                                $data[$k]['createdPCID'] = $this->common_data['current_pc'];
                                $data[$k]['createdUserID'] = $this->common_data['current_userID'];
                                $data[$k]['createdUserName'] = $this->common_data['current_user'];
                                $data[$k]['createdUserGroup'] = $this->common_data['user_group'];
                                $data[$k]['createdDateTime'] = $current_date;

                                $k++;
                            }
                        }

                    }
                    else{
                        $canPull = false;
                        $notMatchRecords[] = ' &nbsp;&nbsp;- '.$row[0]['empCodeName'].' - '.$empID;
                    }


                }

                if($canPull == false){
                    $msg = '';
                    if(!empty($alreadyPayrollProcessed)){
                        $msg .= '<strong>Payroll already processed for following employees</strong><br/>';
                        $msg .= implode('<br/>' , $alreadyPayrollProcessed);
                    }
                    if(!empty($dischargedList)){
                        $msg .= ($msg == '')? '':'<br/>' ;
                        $msg .= '<strong>Following employees already discharged</strong><br/>';
                        $msg .= implode('<br/>' , $dischargedList);
                    }
                    if(!empty($notMatchRecords)){
                        $msg .= ($msg == '')? '':'<br/>' ;
                        //$msg .= '<strong>Following record are not match with the database</strong><br/>(Please verify the salary declaration)<br/>';
                        $msg .= '<strong>Please verify following records with salary declarations</strong><br/>';
                        $msg .= implode('<br/>' , $notMatchRecords);
                    }

                    die( json_encode(['m', $msg]) );
                }


                if(!empty($data)){

                    $this->db->trans_start();
                    $this->db->insert_batch($tableName, $data);
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        die( json_encode(['e', 'Error in process']) );
                    } else {
                        $this->db->trans_commit();
                        die( json_encode(['s', 'Successfully uploaded']) );
                    }

                }
            }
            else{
                die( json_encode(['e', 'File is empty']) );
            }

        } else {
            echo json_encode(['e', 'Please Select CSV File .']);
        }
    }

    public function edit_monthAddition()
    {
        echo json_encode($this->Employee_model->edit_monthAddition('MA'));
    }

    public function edit_monthDeduction()
    {
        echo json_encode($this->Employee_model->edit_monthAddition('MD'));
    }

    public function delete_monthAddition()
    {
        echo json_encode($this->Employee_model->delete_monthAddition('Addition'));
    }

    public function delete_monthDeduction()
    {
        echo json_encode($this->Employee_model->delete_monthAddition('Deduction'));
    }

    public function load_empMonthAddition()
    {
        /*echo json_encode($this->Employee_model->load_empMonthAddition('MA'));*/
        $monthType = $this->input->get('addDeduction');
        $id = $this->input->get('editID');
        if ($monthType == 'MA') {
            $declarationType = 'A';
            $tableName = 'srp_erp_pay_monthlyadditiondetail';
            $masterID = 'monthlyAdditionsMasterID';
        } elseif ($monthType == 'MD') {
            $declarationType = 'D';
            $tableName = 'srp_erp_pay_monthlydeductiondetail';
            $masterID = 'monthlyDeductionMasterID';
        }

        //$con = "IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')";
        $con = " IFNULL(Ename2, '') ";
        /*$this->datatables->select($tableName.'.*, EIdNo, ECode, CONCAT(' . $con . ') AS empName, companyLocalAmount, companyLocalCurrencyDecimalPlaces, transactionCurrency,
                                  transactionAmount, transactionCurrencyDecimalPlaces, description, companyLocalExchangeRate, companyLocalCurrencyDecimalPlaces,
                                  GLSecondaryCode')*/
        $this->datatables->select($tableName . '.*, EIdNo, ECode, CONCAT(' . $con . ') AS empName, companyLocalAmount, IFNULL(declarationID, 0) AS declarationID, IFNULL(GLSecondaryCode, 0) AS GLSecondaryCode, companyLocalCurrencyDecimalPlaces, transactionCurrency, transactionAmount, transactionCurrencyDecimalPlaces, description, companyLocalExchangeRate, companyLocalCurrencyDecimalPlaces')
            ->from($tableName)
            ->join('srp_employeesdetails AS empTB', $tableName . '.empID=empTB.EIdNo')
            ->join('srp_erp_pay_monthlydeclarationstypes AS decType', 'decType.monthlyDeclarationID=' . $tableName . '.declarationID', 'left')
            ->join('srp_erp_chartofaccounts AS chartAcc', 'chartAcc.GLAutoID=' . $tableName . '.GLCode', 'left')
            ->add_column('description', '$1', 'des(description)')
            ->add_column('amount', '$1', 'monthlyAmount(transactionAmount, transactionCurrencyDecimalPlaces, EIdNo, companyLocalExchangeRate)')
            ->add_column('localAmount', '$1', 'localAmount(companyLocalAmount, companyLocalCurrencyDecimalPlaces)')
            ->add_column('exRate', '$1', 'exRate(companyLocalExchangeRate)')
            ->add_column('action', '$1', 'action(EIdNo, transactionCurrency,transactionCurrencyDecimalPlaces)')
            ->where($masterID, $id);

        echo $this->datatables->generate();
    }

    public function load_empMonthDeduction()
    {
        echo json_encode($this->Employee_model->load_empMonthAddition('MD'));
    }

    public function referBack_monthAddition()
    {
        echo json_encode($this->Employee_model->referBack_monthAddition());
    }

    public function amountConversion()
    {
        $amount = $this->input->post('amount');
        $empID = $this->input->post('empID');
        $com_currency = $this->common_data['company_data']['company_default_currency'];
        $com_currencyDPlace = $this->common_data['company_data']['company_default_decimal'];
        $empCurrencyCode = get_employee_currency($empID, 'c_code');

        $tr_amount = (!empty($amount)) ? str_replace(',', '', $amount) : 0;
        $localCon = currency_conversion($empCurrencyCode, $com_currency, $tr_amount);
        $localAmount = ($localCon['conversion'] > 0) ? round(($tr_amount / $localCon['conversion']), 3) : round($tr_amount, 3);
        $returnAm = round($localAmount, $com_currencyDPlace);

        echo json_encode(array($returnAm, round($localCon['conversion'], 6)));
    }

    public function bankBranches()
    {
        echo json_encode($this->Employee_model->bankBranches());
    }

    public function monthlyAD_print()
    {
        $type = $this->uri->segment(3);
        $id = $this->uri->segment(4);

        $data['masterData'] = $this->Employee_model->edit_monthAddition($type, $id);
        $data['details'] = $this->Employee_model->empMonthAddition_printData($type, $id);
        $data['type'] = $type;

        /*echo '<pre>';print_r( $data['details'] );echo '</pre>';die();*/
        /*echo $this->load->view('system\hrm\print\monthAdd_print', $data,true);die();*/

        $html = $this->load->view('system\hrm\print\monthlyAD_print', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4', $data['masterData']['confirmedYN']);
    }

    public function removeAll_emp()
    {
        echo json_encode($this->Employee_model->removeAll_emp());
    }

    public function removeSingle_emp()
    {
        $monthlyAD = $this->input->post('type_m'); // MA | MD
        $masterID = $this->input->post('updateID');
        $updateCode = $this->input->post('updateCode');
        $masterData = $this->Employee_model->edit_monthAddition($monthlyAD, $masterID);

        if ($masterData['isProcessed'] == 1) {
            exit(json_encode(['e', $updateCode . ' is already processed you can not make changes on this.']));
        }

        if ($masterData['confirmedYN'] == 1) {
            exit(json_encode(['e', $updateCode . ' is already confirmed you can not make changes on this.']));
        }

        echo json_encode($this->Employee_model->removeSingle_emp());

    }

    public function fetch_leaveTypes()
    {
        //isPlanApplicable, isSickLeave, sortOrder
        $this->datatables->select('leaveTypeID AS ID , description, attachmentRequired, isPaidLeave, isPlanApplicable, isSickLeave, isExist, sortOrder,
               typeConfirmed, isPlanApplicable AS planAppStr, isSickLeave AS sickLeaveStr',
            false)
            ->from('srp_erp_leavetype')
            ->join(' (SELECT leaveTypeID AS isExist FROM srp_erp_leavegroupdetails  GROUP BY leaveTypeID) AS t1', 't1.isExist=srp_erp_leavetype.leaveTypeID', 'left')
            ->add_column('action', '$1', 'action_leaveTypes(ID, description, isExist, isPaidLeave, attachmentRequired, isPlanApplicable, isSickLeave)')
            ->add_column('sortOrderStr', '<div align="center">$1</div>', 'sortOrder')
            ->where('companyID', current_companyID());
        echo $this->datatables->generate();
    }

    public function save_leaveTypes()
    {
        $this->form_validation->set_rules('leaveDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('attachmentRequired', 'Attachment', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_leaveTypes());
        }
    }

    public function update_leaveTypes()
    {
        $this->form_validation->set_rules('editID', 'Leave ID', 'trim|required');
        $this->form_validation->set_rules('leaveDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('attachmentRequired', 'Attachment', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->update_leaveTypes());
        }
    }

    public function save_sickLeaveCategory(){
        $this->form_validation->set_rules('leaveEditID', 'Leave ID', 'trim|required');
        $this->form_validation->set_rules('salaryCategoryID', 'Salary category', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $companyID = current_companyID();
        $leaveID = $this->input->post('leaveEditID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $salaryCategoryID = $this->input->post('salaryCategoryID');


        $data = array(
            'salaryCategoryID' => $salaryCategoryID,
        );

        $id = $this->db->query("SELECT id FROM srp_erp_sickleavesetup WHERE companyID={$companyID} AND
                                leaveTypeID={$leaveID} AND isNonPayroll='{$isNonPayroll}'")->row('id');

        if(!empty($id)){

            $data['modifiedPCID'] = current_pc();
            $data['modifiedUserID'] = current_userID();
            $data['modifiedDateTime'] = current_date();
            $data['modifiedUserName'] = current_employee();

            $where = [
                'companyID' => $companyID,
                'id' => $id
            ];

            $this->db->where($where)->update('srp_erp_sickleavesetup', $data);
        }
        else{

            $data['leaveTypeID'] = $leaveID;
            $data['isNonPayroll'] = $isNonPayroll;
            $data['companyID'] = $companyID;
            $data['companyCode'] = current_companyCode();
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdUserName'] = current_employee();
            $data['createdDateTime'] = current_date();

            $this->db->insert('srp_erp_sickleavesetup', $data);
        }



        echo json_encode( ['s', 'Salary category updated successfully.'] );
    }

    public function save_sickLeaveSetup(){
        $this->form_validation->set_rules('sortOrder', 'Short order', 'trim|required');
        $this->form_validation->set_rules('leaveEditID', 'Leave ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $companyID = current_companyID();
        $leaveID = $this->input->post('leaveEditID');
        $sortOrder = $this->input->post('sortOrder');
        $isConfirmed = $this->input->post('isConfirmed');

        if($sortOrder < 1){
             die( json_encode(['e', 'Sort order is not valid.']) );
        }

        $validateSortOrder = $this->db->query("SELECT leaveTypeID FROM srp_erp_leavetype WHERE companyID={$companyID}
                                               AND sortOrder={$sortOrder} AND isSickLeave=1")->row('leaveTypeID');

        if(!empty($validateSortOrder)){
            if($validateSortOrder != $leaveID){
                die( json_encode(['e', 'Sort order is not valid.']) );
            }
        }

        if($isConfirmed == 1){
            $isNonSalaryProcess = getPolicyValues('NSP', 'All');
            $result = $this->db->query("SELECT salaryCategoryID, formulaString, isNonPayroll FROM srp_erp_sickleavesetup
                                        WHERE companyID={$companyID} AND leaveTypeID={$leaveID}")->result_array();

            if($isNonSalaryProcess == 1 && count($result) != 2){
                die( json_encode(['e', 'Please configure the sick leave setup for both Payroll and Non payroll.<br/>And than confirm the leave']) );
            }

            if(!empty($result)){
                $errMsg = '';
                $type = '';
                foreach($result as $row){

                    if($isNonSalaryProcess == 1){
                        $type = ($row['isNonPayroll'] == 'N')? 'for payroll' : 'for non-payroll';
                    }

                    if( trim($row['salaryCategoryID']) == '' ){
                        $errMsg .= 'Salary category is not set '.$type.' <br/>';
                    }

                    if( trim($row['formulaString']) == '' ){
                        $errMsg .= 'Formula is not set '.$type.' <br/>';
                    }
                }

                if($errMsg != ''){
                    die( json_encode(['e', $errMsg]) );
                }
            }else{
                die( json_encode(['e', 'Please configure the sick leave setup.<br/>And confirm the leave']) );
            }

        }


        $data['sortOrder'] = $sortOrder;
        $data['typeConfirmed'] = $isConfirmed;
        $data['modifiedPCID'] = current_pc();
        $data['modifiedUserID'] = current_userID();
        $data['modifiedDateTime'] = current_date();
        $data['modifiedUserName'] = current_employee();

        $where = [
            'companyID' => $companyID,
            'leaveTypeID' => $leaveID
        ];

        $this->db->where($where)->update('srp_erp_leavetype', $data);


        echo json_encode( ['s', 'Sick leave setup updated successfully.'] );
    }

    public function save_sickLeaveFormula(){
        $companyID = current_companyID();
        $formulaString = trim($this->input->post('formulaString'));
        $leaveID = $this->input->post('payGroupID');
        $salaryCategories = $this->input->post('salaryCategoryContainer');
        $salaryCategories = (trim($salaryCategories) == '') ? null : $salaryCategories;

        $postData = explode('|', $leaveID);
        $leaveID = $postData[0];
        $isNonPayroll = $postData[1];

        $data = array(
            'formulaString' => $formulaString,
            'salaryCategories' => $salaryCategories,
        );

        $masterData = $this->db->query("SELECT id, typeConfirmed FROM srp_erp_leavetype AS lType
                                        LEFT JOIN (
                                          SELECT id, leaveTypeID FROM srp_erp_sickleavesetup WHERE leaveTypeID={$leaveID} AND
                                          isNonPayroll='{$isNonPayroll}' AND companyID={$companyID}
                                        ) AS setupTB ON lType.leaveTypeID=setupTB.leaveTypeID
                                        WHERE lType.companyID={$companyID} AND lType.leaveTypeID={$leaveID} ")->row_array();

        if($masterData['typeConfirmed'] == 1 && $formulaString == ''){
            die( json_encode( ['e', 'This leave type is confirmed.<br/>You can not save the formula as blank'] ) );
        }

        if(!empty($masterData['id'])){

            $data['modifiedPCID'] = current_pc();
            $data['modifiedUserID'] = current_userID();
            $data['modifiedDateTime'] = current_date();
            $data['modifiedUserName'] = current_employee();

            $where = [
                'companyID' => $companyID,
                'leaveTypeID' => $leaveID
            ];

            $this->db->where($where)->update('srp_erp_sickleavesetup', $data);
        }
        else{

            $data['leaveTypeID'] = $leaveID;
            $data['isNonPayroll'] = $isNonPayroll;
            $data['companyID'] = $companyID;
            $data['companyCode'] = current_companyCode();
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdUserName'] = current_employee();
            $data['createdDateTime'] = current_date();

            $this->db->insert('srp_erp_sickleavesetup', $data);
        }

        echo json_encode( ['s', 'Formula updated successfully.'] );
    }

    public function delete_leaveTypes()
    {
        $this->form_validation->set_rules('deleteID', 'Leave ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->delete_leaveTypes());
        }
    }

    public function load_empLeaveView()
    {
        $data['empID'] = $this->input->post('empID');
        $data['leaves'] = $this->Employee_model->emp_leaves();

        $this->load->view('system\hrm\load_empLeaveView', $data);
    }

    public function save_empLeaveEntitle()
    {
        $this->form_validation->set_rules('leaveType[]', 'Leave Type', 'trim|required|numeric');
        $this->form_validation->set_rules('leave_days[]', 'Days', 'trim|required|numeric');
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_empLeaveEntitle());
        }
    }

    public function update_empLeaveEntitle()
    {
        $this->form_validation->set_rules('leaveType_e', 'Leave Type', 'trim|required|numeric');
        $this->form_validation->set_rules('leave_days_e', 'Days', 'trim|required|numeric');
        $this->form_validation->set_rules('editID', 'Edit ID', 'trim|required|numeric');
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->update_empLeaveEntitle());
        }
    }

    public function delete_empLeaveEntitle()
    {
        $this->form_validation->set_rules('deleteID', 'Delete ID', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->delete_empLeaveEntitle());
        }
    }

    public function employeeData()
    {
        echo json_encode($this->Employee_model->employeeData());
    }

    public function employeeLeaveSummery()
    {
        $companyID = current_companyID();
        $empID = $this->input->post('empID');
        /*      $policy = $this->db->query("select isMonthly from `srp_erp_leavegroup` WHERE companyID={$companyID}
       AND leaveGroupID=(SELECT leaveGroupID FROM `srp_employeesdetails` WHERE EidNo = {$empID})")->row_array();*/
        $_POST['policyMasterID'] = $this->input->post('policyMasterID');
        echo json_encode($this->Employee_model->employeeLeaveSummery());
    }

    public function save_employeeLeave()
    {
        $this->form_validation->set_rules('leaveType', 'Leave Type', 'trim|required|numeric');
        $this->form_validation->set_rules('startDate', 'Start Date', 'trim|required|date');
        $this->form_validation->set_rules('endDate', 'End Date', 'trim|required|date');
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required|numeric');
        //$this->form_validation->set_rules('comment', 'Comment', 'trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $endDate = $this->input->post('endDate');
            $isPayrollProcessed = isPayrollProcessed($endDate);

            if ($isPayrollProcessed['status'] == 'N') {
                echo json_encode($this->Employee_model->save_employeeLeave());
            } else {
                $greaterThanDate = date('Y - F', strtotime($isPayrollProcessed['year'] . '-' . $isPayrollProcessed['month'] . '-01'));
                echo json_encode(array('e', 'Leave date should be  <p> greater than [ ' . $greaterThanDate . ' ] '));
            }
        }
    }

    function get_covering_employee_list(){
        $empID = $this->input->post('empID');
        $coveringEmp = $this->input->post('coveringEmp');
        $confirmedYN = $this->input->post('confirmedYN');
        $companyID = current_companyID();

        $html = '<select id="coveringEmpID" name="coveringEmpID" class="form-control coveringEmp frm_input">';
        $html .= '<option value="">Select a employee</option>';

        if($confirmedYN == 1){
            $empData = $this->db->query("SELECT EIdNo, ECode, Ename2 FROM srp_employeesdetails
                       WHERE Erp_companyID={$companyID} AND EIdNo={$coveringEmp}")->row_array();
            $html .= '<option value="'.$empData['EIdNo'].'" selected>'.$empData['ECode'].' - '.$empData['Ename2'].'</option>';
        }

        if(!empty($empID) && $confirmedYN != 1){

            $empList = $this->db->query("SELECT * FROM (
                                        SELECT EIdNo, ECode, Ename2 FROM srp_employeesdetails AS empTB
                                        JOIN srp_erp_employeemanagers AS mangerTB ON mangerTB.empID=empTB.EIdNo
                                        WHERE Erp_companyID={$companyID} AND empConfirmedYN=1 AND isDischarged=0
                                        AND isSystemAdmin=0 AND mangerTB.active=1 AND companyID={$companyID}
                                        AND EIdNo != {$empID}
                                        AND managerID = (
                                            SELECT managerID FROM srp_erp_employeemanagers WHERE empID={$empID} AND active=1
                                        )
                                        UNION
                                        SELECT EIdNo, ECode, Ename2 
                                        FROM srp_erp_employeemanagers manTB
                                        JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = manTB.managerID
                                        WHERE empID={$empID} AND active=1
                                        UNION 
                                        SELECT EIdNo, ECode, Ename2 FROM srp_employeesdetails AS empTB
                                        WHERE Erp_companyID={$companyID} AND empConfirmedYN=1 AND isDischarged=0
                                        AND isSystemAdmin=0 
                                        AND EIdNo IN (
                                            SELECT empID FROM srp_erp_employeemanagers WHERE managerID={$empID} AND active=1
                                        )
                                     ) AS t1 ORDER BY Ename2")->result_array();
            
            if(!empty($empList)){
                foreach($empList as $val){
                    $selected = ($coveringEmp == $val['EIdNo'])? 'selected': '';
                    $html .= '<option value="'.$val['EIdNo'].'" '.$selected.'>'.$val['ECode'].' - '.$val['Ename2'].'</option>';
                }
            }
        }

        $html .= '</select>';

        echo $html;
    }

    public function fetch_employee_leave()
    {
        $com = current_companyID();
        $where = "srp_erp_leavemaster.companyID = {$com}";
        $currentEmpID = $this->input->post('currentEmpID');
        if ($currentEmpID != '') {
            $where .= " AND empID={$currentEmpID}";
        }

        $filterDateFrom = $this->input->post('filterDateFrom');
        $filterDateFrom = input_format_date($filterDateFrom, date_format_policy());
        $filterDateTo = $this->input->post('filterDateTo');
        $filterDateTo = input_format_date($filterDateTo, date_format_policy());
        $empFilter = $this->input->post('empFilter');
        $empFilter = implode(',', $empFilter);
        $status = $this->input->post('status');

        $where .= " AND startDate BETWEEN '{$filterDateFrom}' AND '{$filterDateTo}'";
        $where .= ($empFilter!= '')? " AND empID IN ({$empFilter})": '';

        if ($status != 'all') {
            switch ($status){
                case 'draft':
                    $where .= " AND confirmedYN = 0 ";
                break;

                case 'confirmed':
                    $where .= " AND confirmedYN = 1 AND approvedYN = 0 ";
                break;

                case 'approved':
                    $where .= " AND approvedYN = 1 AND ( requestForCancelYN = 0 OR requestForCancelYN IS NULL )";
                break;

                case 'canReq':
                    $where .= " AND requestForCancelYN = 1 AND cancelledYN = 0";
                break;

                case 'canApp':
                    $where .= " AND cancelledYN = 1 ";
                break;
            }
        }



        $convertFormat = convert_date_format_sql();

        $this->datatables->select('leaveMasterID, documentCode, ECode, CONCAT(ECode, \' - \', Ename2) AS empName, confirmedYN, approvedYN, description, requestForCancelYN,
            IF(ISNULL(requestForCancelYN), approvedYN, IF(requestForCancelYN=1, 5, 6)) AS requestStr, DATE_FORMAT(startDate,\'' . $convertFormat . '\') AS startDate, 
            DATE_FORMAT(endDate,\'' . $convertFormat . '\') AS endDate, IF(applicationType=1, \'Apply Leave\', \'Leave Plan\') AS appDes, cancelledYN
            ', true)
            ->from('srp_erp_leavemaster')
            ->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_leavemaster.empID')
            ->join('srp_erp_leavetype', 'srp_erp_leavetype.leaveTypeID = srp_erp_leavemaster.leaveTypeID')
            ->add_column('confirm', '$1', 'confirm(confirmedYN)')
            ->add_column('approved', '$1', 'confirm_ap_user(requestStr,confirmedYN,"LA",leaveMasterID,1)')
            ->add_column('action', '$1', 'leaveApplicationAction(leaveMasterID, documentCode, confirmedYN, approvedYN, requestForCancelYN, cancelledYN)')
            ->where($where);

        echo $this->datatables->generate();
    }

    /*public function fetch_leave_conformation()
    {
        $userID = $this->common_data["current_userID"];
        //$con = "Ename1,'  ',Ename2, ' ',Ename3";

        $con = "IFNULL(Ename2, '')";
        $status = trim($this->input->post('approvedYN'));

        $where = array(
            'approve.documentID' => 'LA',
            'ap.documentID' => 'LA',
            'ap.employeeID' => $userID,
            'approve.approvedYN' => $status,

        );
        $this->datatables->select('leaveMasterID, CONCAT(' . $con . ') AS empName, ECode, t1.documentCode as documentCode , confirmedYN, approve.approvedYN as approvedYN , documentApprovedID, approvalLevelID')
            ->from('srp_erp_leavemaster AS t1')
            ->join('srp_erp_documentapproved AS approve', 'approve.documentSystemCode = t1.leaveMasterID AND approve.approvalLevelID = t1.currentLevelNo')
            ->join('srp_employeesdetails AS emp', 'emp.EIdNo = t1.empID')
            ->join('srp_erp_approvalusers AS ap', 'ap.levelNo = t1.currentLevelNo')
            ->where($where)
            ->where('ap.companyID', current_companyID())
            ->where('t1.companyID', current_companyID())
            ->add_column('level', "<center>Level $1</center>", 'approvalLevelID')
            ->add_column('approved', '$1', 'confirm(approvedYN)')
            ->add_column('edit', '$1', 'leave_action_approval(leaveMasterID, approvalLevelID, documentCode)')
            ->add_column('documentCode', '<a onclick=\'load_emp_leaveDet("$2","$4"); \'>$1</a>', 'documentCode,leaveMasterID,documentApprovedID,approvalLevelID');

        echo $this->datatables->generate();
    }*/

    public function employeeLeave_details()
    {
        $masterID = $this->input->post('masterID');
        /*   $leave= $this->db->query("select policyMasterID from `srp_erp_leavemaster` WHERE leaveMasterID={$masterID}")->row_array();
           $policyMasterID= $leave['policyMasterID'];*/
        $leaveDet = $this->Employee_model->employeeLeave_details($masterID);
        $empDet = $this->Employee_model->getemployeedetails($leaveDet['empID']);

        $entitleDet = $this->Employee_model->employeeLeaveSummery($leaveDet['empID'], $leaveDet['leaveTypeID'], $leaveDet['policyMasterID']);

        echo json_encode(
            array(
                'leaveDet' => $leaveDet,
                'empDet' => $empDet,
                'entitleDet' => $entitleDet
            )
        );
    }

    public function refer_back_empLeave()
    {

        $leaveMasterID = trim($this->input->post('refID'));
        $leaveDet = $this->Employee_model->employeeLeave_details($leaveMasterID);

        if($leaveDet['approvedYN'] == 1){
            die( json_encode(['e', 'This document already approved. You can not refer backed this.']) );
        }

        $companyID = current_companyID();

        $data = array(
            'confirmedYN' => 0,
            'confirmedDate' => null,
            'confirmedByEmpID' => null,
            'confirmedByName' => null,
        );

        $this->db->trans_start();

        $this->db->where('leaveMasterID', $leaveMasterID);
        $this->db->where('companyID', $companyID);
        $this->db->update('srp_erp_leavemaster', $data);

        $this->db->where('companyID', $companyID);
        $this->db->where('departmentID', 'LA');
        $this->db->where('documentSystemCode', $leaveMasterID);
        $this->db->delete('srp_erp_documentapproved');


        /*** Delete accrual leave ***/
        $this->db->where('companyID', $companyID);
        $this->db->where('leaveMasterID', $leaveMasterID);
        $this->db->delete('srp_erp_leaveaccrualmaster');

        $this->db->where('leaveMasterID', $leaveMasterID);
        $this->db->delete('srp_erp_leaveaccrualdetail');

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            echo json_encode(array('s', ' Referred Back Successfully.'));
        } else {
            $this->db->trans_rollback();
            echo json_encode(array('e', ' Error in refer back.'));
        }
    }

    public function refer_back_empLeave_cancellation()
    {

        $leaveMasterID = trim($this->input->post('refID'));
        $companyID = current_companyID();
        $leave = $this->db->query("SELECT leaveMaster.*, empTB.Ename2, EEmail, ECode AS empCode, coveringEmpID 
                                   FROM srp_erp_leavemaster AS leaveMaster
                                   JOIN srp_employeesdetails AS empTB ON empID=empTB.EIdNo
                                   WHERE leaveMasterID={$leaveMasterID} AND companyID={$companyID}")->row_array();

        if($leave['cancelledYN'] == 1){
            die( json_encode(['e', 'This document already cancelled. You can not refer backed this.']) );
        }

        $level = $leave['currentLevelNo'];
        $empID = $leave['empID'];
        $coveringEmpID = $leave['coveringEmpID'];

        $setupData = getLeaveApprovalSetup();
        $approvalEmp_arr = $setupData['approvalEmp'];
        $approvalLevel = $setupData['approvalLevel'];
        $emp_mail_arr= [];

        if($level <= $approvalLevel) {

            $managers = $this->db->query("SELECT *, {$coveringEmpID} AS coveringEmp FROM (
                                             SELECT repManager
                                             FROM srp_employeesdetails AS empTB
                                             LEFT JOIN (
                                                 SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers
                                                 WHERE active = 1 AND empID={$empID} AND companyID={$companyID}
                                             ) AS repoManagerTB ON empTB.EIdNo = repoManagerTB.empID
                                             WHERE Erp_companyID = '{$companyID}' AND EIdNo={$empID}
                                         ) AS empData
                                         LEFT JOIN (
                                              SELECT managerID AS topManager, empID AS topEmpID
                                              FROM srp_erp_employeemanagers WHERE companyID={$companyID} AND active = 1
                                         ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID")->row_array();

            $approvalSetup = $setupData['approvalSetup'];
            $x = 1;

            while($x <= $approvalLevel) {
                if($x > $level){ /* Proceed up to current approval level */
                    break;
                }

                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $x);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';

                if($approvalType == 3){
                    $nextApprovalEmp_arr = (array_key_exists($x, $approvalEmp_arr)) ? $approvalEmp_arr[$x] : null;
                    if(!empty($nextApprovalEmp_arr)){
                        foreach($nextApprovalEmp_arr as $hrMangers){
                            if( !in_array($hrMangers['empID'], $emp_mail_arr)){
                                $emp_mail_arr[] = $hrMangers['empID'];
                            }
                        }
                    }
                }
                else{
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    if( !empty($managers[$managerType]) ){
                        $nextApprovalEmpID = $managers[$managerType];
                        if( !in_array($nextApprovalEmpID, $emp_mail_arr) && $nextApprovalEmpID !=  ''){
                            $emp_mail_arr[] = $nextApprovalEmpID;
                        }
                    }

                }

                $x++;

            }
        }

        if(!empty($emp_mail_arr)){
            $emp_mail_arr = implode(',', $emp_mail_arr);

            $empData = $this->db->query("SELECT EIdNo, Ename2, EEmail FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                            AND EIdNo IN ({$emp_mail_arr})")->result_array();
            
            foreach($empData as $eData){

                $bodyData = 'Leave cancellation ' . $leave['documentCode'] . ' is refer backed.<br/> ';
                $param["empName"] = $eData["Ename2"];
                $param["body"] = $bodyData;

                $mailData = [
                    'approvalEmpID' => $eData["EIdNo"],
                    'documentCode' => $leave['documentCode'],
                    'toEmail' => $eData["EEmail"],
                    'subject' => 'Leave cancellation refer backed',
                    'param' => $param
                ];

                send_approvalEmail($mailData);
            }
        }


        $data = array(
            'requestForCancelYN' => null,
            'cancelRequestedDate' => null,
            'cancelRequestByEmpID' => null,
            'cancelRequestComment' => null,
        );

        $this->db->trans_start();

        $this->db->where('leaveMasterID', $leaveMasterID);
        $this->db->where('companyID', $companyID);
        $this->db->update('srp_erp_leavemaster', $data);

        $this->db->where('companyID', $companyID);
        $this->db->where('departmentID', 'LA');
        $this->db->where('isCancel', 1);
        $this->db->where('documentSystemCode', $leaveMasterID);
        $this->db->delete('srp_erp_documentapproved');


        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            echo json_encode(array('s', 'Leave Cancellation Referred Back Successfully.'));
        } else {
            $this->db->trans_rollback();
            echo json_encode(array('e', ' Error in leave cancellation refer back.'));
        }
    }

    public function cancel_leave()
    {
        $leaveMasterID = trim($this->input->post('cancelID'));
        $comments = trim($this->input->post('comments'));
        $companyID = current_companyID();
        $current_userID = current_userID();
        $leave = $this->db->query("SELECT leaveMaster.*, empTB.Ename2, EEmail, ECode AS empCode, coveringEmpID
                                   FROM srp_erp_leavemaster AS leaveMaster
                                   JOIN srp_employeesdetails AS empTB ON empID=empTB.EIdNo
                                   WHERE leaveMasterID={$leaveMasterID} AND companyID={$companyID}")->row_array();
        $level = 1;

        if($leave['approvedYN'] != 1){
            die( json_encode(['e', 'This document not confirmed yet.']) );
        }

        if($leave['cancelledYN'] == 1){
            die( json_encode(['e', 'This document already canceled.']) );
        }

        if($leave['requestForCancelYN'] == 1){
            die( json_encode(['e', 'This document already in cancel request.']) );
        }

        $this->db->trans_start();


        $empID = $leave['empID'];
        $coveringEmpID = $leave['coveringEmpID'];
        $setupData = getLeaveApprovalSetup();
        $approvalEmp_arr = $setupData['approvalEmp'];
        $approvalLevel = $setupData['approvalLevel'];
        $isManagerAvailableForNxtApproval = 0;
        $nextLevel = null;
        $nextApprovalEmpID = null;
        $data_app = [];


        /**** If the number of approval level is less than current approval than only this process will run ****/
        if($level <= $approvalLevel){

            $managers = $this->db->query("SELECT *, {$coveringEmpID} AS coveringEmp FROM (
                                             SELECT repManager
                                             FROM srp_employeesdetails AS empTB
                                             LEFT JOIN (
                                                 SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers
                                                 WHERE active = 1 AND empID={$empID} AND companyID={$companyID}
                                             ) AS repoManagerTB ON empTB.EIdNo = repoManagerTB.empID
                                             WHERE Erp_companyID = '{$companyID}' AND EIdNo={$empID}
                                         ) AS empData
                                         LEFT JOIN (
                                              SELECT managerID AS topManager, empID AS topEmpID
                                              FROM srp_erp_employeemanagers WHERE companyID={$companyID} AND active = 1
                                         ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID")->row_array();

            $approvalSetup = $setupData['approvalSetup'];
            $x = $level;


            /**** Validate is there a manager available for next approval level ****/

            $i = 0;

            while($x <= $approvalLevel) {

                $isCurrentLevelApproval_exist = 0;
                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $x);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';

                if($approvalType == 3){
                    $isCurrentLevelApproval_exist = 1;

                    if($isManagerAvailableForNxtApproval == 0){
                        $nextLevel = $x;
                        $nextApprovalEmpID = (array_key_exists($x, $approvalEmp_arr)) ? $approvalEmp_arr[$x] : '';
                        $isManagerAvailableForNxtApproval = 1;
                    }
                }
                else{
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    if( !empty($managers[$managerType]) ){
                        $isCurrentLevelApproval_exist = 1;

                        if($isManagerAvailableForNxtApproval == 0){
                            $nextLevel = $x;
                            $nextApprovalEmpID = $managers[$managerType];
                            $isManagerAvailableForNxtApproval = 1;
                        }
                    }

                }

                if($isCurrentLevelApproval_exist == 1){
                    $data_app[$i]['companyID'] = $companyID;
                    $data_app[$i]['companyCode'] = current_companyCode();
                    $data_app[$i]['departmentID'] = 'LA';
                    $data_app[$i]['documentID'] = 'LA';
                    $data_app[$i]['documentSystemCode'] = $leaveMasterID;
                    $data_app[$i]['documentCode'] = $leave['documentCode'];
                    $data_app[$i]['isCancel'] = 1;
                    $data_app[$i]['table_name'] = 'srp_erp_leavemaster';
                    $data_app[$i]['table_unique_field_name'] = 'leaveMasterID';
                    $data_app[$i]['documentDate'] = current_date();
                    $data_app[$i]['approvalLevelID'] = $x;
                    $data_app[$i]['roleID'] = null;
                    $data_app[$i]['approvalGroupID'] = current_user_group();
                    $data_app[$i]['roleLevelOrder'] = null;
                    $data_app[$i]['docConfirmedDate'] = current_date();
                    $data_app[$i]['docConfirmedByEmpID'] = $current_userID;
                    $data_app[$i]['approvedEmpID'] = null;
                    $data_app[$i]['approvedYN'] = 0;
                    $data_app[$i]['approvedDate'] = null;
                    $i++;
                }

                $x++;
            }

        }

        if(!empty($data_app)){

            $this->db->insert_batch('srp_erp_documentapproved', $data_app);

            $upData = [
                'cancelledYN' => 0,
                'requestForCancelYN' => 1,
                'cancelRequestedDate' => current_date(),
                'cancelRequestComment' => $comments,
                'cancelRequestByEmpID' => current_userID(),
                'currentLevelNo' => $nextLevel,
                'modifiedPCID' => current_pc(),
                'modifiedUserID' => $current_userID,
                'modifiedUserName' => current_employee(),
                'modifiedDateTime' => current_date()
            ];
            $this->db->where('leaveMasterID', $leaveMasterID);
            $update = $this->db->update('srp_erp_leavemaster', $upData);

            if($update){
                $leaveBalanceData = $this->Employee_model->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID']);
                $balanceLeave = $leaveBalanceData['balance'];
                //$balanceLeave = ($balanceLeave > 0)?  ($balanceLeave - $leave['days']) : 0;

                if(is_array($nextApprovalEmpID)){
                    /**** If the approval type HR there may be more than one employee for next approval process ****/
                    $nextApprovalEmpID = implode(',', array_column($nextApprovalEmpID, 'empID'));
                }

                $nxtEmpData_arr = $this->db->query("SELECT EIdNo, Ename2, EEmail FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                            AND EIdNo IN ({$nextApprovalEmpID})")->result_array();

                foreach($nxtEmpData_arr as $nxtEmpData){

                    $param["empName"] = $nxtEmpData["Ename2"];
                    $param["body"] = 'Leave application cancellation ' . $leave['documentCode'] . ' is pending for your approval.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Name </td><td> : '.$leave['Ename2'].' - '.$leave['empCode'].'</td></tr>
                                      <tr><td><strong>Start Date</td><td> : '.date('Y-m-d', strtotime($leave['startDate'])).'</td></tr>
                                      <tr><td><strong>End Date</td><td> : '.date('Y-m-d', strtotime($leave['endDate'])).'</td></tr>
                                      <tr><td><strong>Leave type </td><td> : '.$leaveBalanceData['description'].'</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : '.$balanceLeave.'</td></tr>
                                  </table>';

                    $mailData = [
                        'approvalEmpID' => $nxtEmpData["EIdNo"],
                        'documentCode' => $leave['documentCode'],
                        'toEmail' => $nxtEmpData["EEmail"],
                        'subject' => 'Leave Cancellation Approval',
                        'param' => $param
                    ];

                    send_approvalEmail($mailData);
                }

                $this->db->trans_commit();
                die( json_encode(['s', 'Leave cancellation approval created successfully.']) );


            }
            else{
                $this->db->trans_rollback();
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                die( json_encode(['e', $common_failed]) );
            }

        }
    }

    public function delete_empLeave()
    {
        echo json_encode($this->Employee_model->delete_empLeave());
    }

    public function load_emp_leaveDet()
    {
        echo json_encode($this->Employee_model->load_emp_leaveDet());
    }

    /*public function leaveApproval()
    {
        $leaveID = $this->input->post('hiddenLeaveID');
        $level_id = $this->input->post('level');
        $status = $this->input->post('status');
        $comments = $this->input->post('comments');

        $this->form_validation->set_rules('hiddenLeaveID', 'Leave ID', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        $this->form_validation->set_rules('level', 'Level', 'trim|required');
        if ($this->input->post('status') == 2) {
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $leaveDet = $this->Employee_model->employeeLeave_details($leaveID);

            $this->load->library('approvals');
            $approvals_status = $this->approvals->approve_document($leaveID, $level_id, $status, $comments, 'LA');
            if ($approvals_status == 1 || $approvals_status == 2) {
                echo json_encode(array('s', 'Leave [ ' . $leaveDet['documentCode'] . ' ] Approved'));
            } else if ($approvals_status == 3) {
                echo json_encode(array('s', '[ ' . $leaveDet['documentCode'] . ' ] Reject Process success.'));
            } else if ($approvals_status == 4) {
                echo json_encode(array('e', '[ ' . $leaveDet['documentCode'] . ' ] Reject Process Failed.', $approvals_status));
            } else if ($approvals_status == 5) {
                echo json_encode(array('w', '[ ' . $leaveDet['documentCode'] . ' ] Previous Level Approval Not Finished.'));
            } else {
                echo json_encode(array('e', 'Error in Leave Approvals Of  [ ' . $leaveDet['documentCode'] . ' ] ', $approvals_status));
            }
        }
    }*/

    public function leave_print()
    {
        $companyID = current_companyID();
        $id = $this->uri->segment(3);

        $data['masterData'] = $this->employeeLeave_detailsOnApproval($id, true);
        $coveringEmpID = $data['masterData']['leaveDet']['coveringEmpID'];

        if(!empty($coveringEmpID)){
            $data['coveringEmp'] = $this->db->query("SELECT CONCAT(ECode,' -', Ename2) coveringEmp FROM srp_employeesdetails WHERE EIdNo='{$coveringEmpID}'")->row('coveringEmp');
        }

        //find leave type
        $leaveTypeID = $leaveType[''] = $data['masterData']['leaveDet']['leaveTypeID'];
        $desArr = $this->db->query("SELECT description FROM srp_erp_leavetype WHERE leaveTypeID={$leaveTypeID} ")->row_array();
        $data['leaveDescription'] = $desArr['description'];

        if (empty($data['masterData'])) {
            show_404();
            die();
        }

        $isApproved = $data['masterData']['leaveDet']['approvedYN'];
        if( $isApproved == 1 ){
            $appData = $this->db->query("SELECT approvedEmpID, approvedComments, approvalLevelID, Ename2, approvedDate, isCancel
                                         FROM srp_erp_documentapproved AS appTB
                                         JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=approvedEmpID
                                         WHERE companyID={$companyID} AND documentID='LA' AND documentSystemCode={$id}
                                         ")->result_array();
            $data['appData'] = $appData;
        }

        $requestForCancelYN = ($data['masterData']['leaveDet']['requestForCancelYN'] == 1)? '1' : '0';

        if($requestForCancelYN == 1){
            $cancelData = $this->db->query("SELECT cancelRequestComment, cancelRequestedDate, Ename2, Ecode
                                         FROM srp_erp_leavemaster AS masterTB
                                         JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=masterTB.cancelRequestByEmpID
                                         WHERE companyID={$companyID} AND leaveMasterID={$id}")->row_array();
            $data['cancelData'] = $cancelData;
        }

        //echo '<pre>'; print_r($data); echo '</pre>';        die();
        $html = $this->load->view('system\hrm\print\leave_print', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4', $isApproved);
    }

    public function employeeLeave_detailsOnApproval($masterID = null, $asReturn = null)
    {
        $companyID = current_companyID();
        $masterID = ($masterID == null) ? $this->input->post('masterID') : $masterID;
        $leaveDet = $this->Employee_model->employeeLeave_details($masterID);
        $empDet = $this->Employee_model->getemployeedetails($leaveDet['empID']);


        if (!empty($leaveDet)) {
            $_POST['policyMasterID'] = $leaveDet['policyMasterID'];
        }
        $entitleDet = $this->Employee_model->employeeLeaveSummery($leaveDet['empID'], $leaveDet['leaveTypeID']);


        $returnArr = array(
            'leaveDet' => $leaveDet,
            'empDet' => $empDet,
            'entitleDet' => $entitleDet
        );

        if ($asReturn == true) {
            return $returnArr;
        } else {
            echo json_encode($returnArr);
        }
    }

    /************* Attendance *********/

    public function fetch_attendanceType()
    {

        $this->datatables->select('AttPresentTypeID, PresentTypeDes')
            ->from('srp_attpresenttype AS t1')
            ->join('srp_sys_attpresenttype AS t2', 't1.SysPresentTypeID=t2.PresentTypeID')
            ->add_column('edit', '$1', 'action_attendanceTypes(AttPresentTypeID, PresentTypeDes)')
            ->where('t1.Erp_companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function savePresentType()
    {
        $this->form_validation->set_rules('attType[]', 'Attendance Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->savePresentType());
        }
    }

    public function delete_attendanceTypes()
    {
        $this->form_validation->set_rules('hidden-id', 'Attendance ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $hiddenID = $this->input->post('hidden-id');
            echo json_encode($this->Employee_model->delete_attendanceTypes($hiddenID));
        }
    }


    public function fetch_attendance()
    {

        //$con = "IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')";
        $con = "IFNULL(Ename2, '')";

        $convertFormat = convert_date_format_sql();
        $this->datatables->select('EmpAttMasterID,DATE_FORMAT(AttDate,\'' . $convertFormat . '\') AS AttDate , DATE_FORMAT(AttTime, "%h:%i %p") AS AttTime, isAttClosed, ECode AS doneByCode, CONCAT(' . $con . ') AS doneByName', true)
            ->from('srp_empattendancemaster AS t1')
            ->join('srp_employeesdetails AS t2', 't1.DoneBy=t2.EIdNo')
            ->add_column('isClosed', '$1', 'confirm(isAttClosed)')
            ->add_column('action', '$1', 'action_attendanceMaster(EmpAttMasterID, isAttClosed, AttDate)')
            ->where('t1.Erp_companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function new_attendance()
    {
        $this->form_validation->set_rules('attendanceDate', 'Attendance Date', 'trim|required|date');
        $this->form_validation->set_rules('attendanceTime', 'Attendance time', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->new_attendance());
        }
    }

    public function load_attendanceEmployees()
    {
        $attID = $this->input->post('attID');
        $attMasterData = $this->Employee_model->getAttMasterData($attID);

        $data['masterData'] = $attMasterData;
        $data['emp_arr'] = $this->Employee_model->get_attendanceEmployees($attID);
        $this->load->view('system\hrm\ajax\load_empAttendanceDetails', $data);
    }

    public function delete_attendanceMaster()
    {
        $this->form_validation->set_rules('hidden-id', 'Attendance ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $hiddenID = $this->input->post('hidden-id');
            $attMasterData = $this->Employee_model->getAttMasterData($hiddenID);

            if ($attMasterData['isAttClosed'] == 1) {
                echo json_encode(array('e', 'This attendance is already closed. You can not delete this.'));
            } else {
                echo json_encode($this->Employee_model->delete_attendanceMaster());
            }
        }
    }

    public function save_attendanceDetails()
    {
        $this->form_validation->set_rules('attendMasterID', 'Attendance ID', 'trim|required');
        $this->form_validation->set_rules('att-emp[]', 'Employee', 'trim|callback_check_employeeOnAttendance');
        $this->form_validation->set_rules('att-type[]', 'Attendance Type', 'trim|callback_check_presentTypeOnAttendance');
        $this->form_validation->set_rules('att-time[]', 'Attendance time', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_attendanceDetails());
        }
    }

    function check_employeeOnAttendance()
    {
        if (empty($this->input->post('att-emp'))) {
            $this->form_validation->set_message('check_employeeOnAttendance', 'There is no employee to proceed');
            return false;
        } else {
            return true;
        }
    }

    function check_presentTypeOnAttendance()
    {
        if (empty($this->input->post('att-type'))) {
            $this->form_validation->set_message('check_presentTypeOnAttendance', 'Select a present type for every employee');
            return false;
        } else {
            return true;
        }
    }


    /*Start of Shift Master*/
    public function fetch_shiftMaster()
    {
        $this->datatables->select('shiftID, Description')
            ->from('srp_erp_pay_shiftmaster AS t1')
            ->add_column('action', '$1', 'action_workShift(shiftID, Description)')
            ->where('companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function saveShiftMaster()
    {

        $this->form_validation->set_rules('shiftDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('masterDayID[]', 'Description', 'callback_validateDutyTime');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->saveShiftMaster());
        }


    }

    function validateDutyTime()
    {
        $onTime_arr = $this->input->post('onTime[]');
        $offTime_arr = $this->input->post('offTime[]');
        $isWeekend_arr = $this->input->post('isWeekend[]');
        $masterDayID_arr = $this->input->post('masterDayID[]');
        $dayDescription_arr = $this->input->post('dayDescription[]');
        $errMsg = null;

        foreach ($masterDayID_arr as $key => $row) {
            if ($isWeekend_arr[$key] == 0) {
                if (trim($onTime_arr[$key]) == '') {
                    $errMsg .= $dayDescription_arr[$key] . ' On time not set </br>';
                }

                if (trim($offTime_arr[$key]) == '') {
                    $errMsg .= $dayDescription_arr[$key] . ' Off time not set </br>';
                }

            }
        }

        if ($errMsg == null) {
            return TRUE;
        } else {
            $this->form_validation->set_message('validateDutyTime', $errMsg);
            return FALSE;
        }

    }

    public function fetch_shiftDetails()
    {
        $this->form_validation->set_rules('shiftID', 'Shift ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->fetch_shiftDetails());
        }
    }


    public function updateShiftMaster()
    {
        $this->form_validation->set_rules('shiftDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('editID', 'Shift ID', 'required');
        $this->form_validation->set_rules('masterDayID[]', 'Description', 'callback_validateDutyTime');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->updateShiftMaster());
        }
    }

    public function deleteShiftMaster()
    {
        $this->form_validation->set_rules('deleteID', 'Shift ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->deleteShiftMaster());
        }
    }


    public function save_ShiftEmp()
    {

        $this->form_validation->set_rules('masterID', 'Shift ID', 'required');
        $this->form_validation->set_rules('employees', 'Employee', 'callback_checkShiftEmployees');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_ShiftEmp());
        }

    }

    function checkShiftEmployees()
    {
        $employees = trim($this->input->post('employees'));
        $emp_arr = json_decode($employees);

        if (count($emp_arr) > 0) {
            return true;
        } else {
            $this->form_validation->set_message('checkShiftEmployees', 'Please Select at least one employee to proceed.' . count($emp_arr));
            return false;
        }
    }
    /*End of Shift Master*/


    /*Start of Attendance review*/
    function attendancePulling()
    {

        $this->form_validation->set_rules('floorID', 'Floor', 'required');
        $this->form_validation->set_rules('upload_fromDate', 'From Date', 'required');
        $this->form_validation->set_rules('upload_toDate', 'To Date', 'required');
        /* $this->form_validation->set_rules('machineMasterID', 'Machine', 'required');*/
        $floorID = $this->input->post('floorID');
        $automatic = true;
        $isManualAttendance = $this->input->post('isManualAttendance');
        if (isset($isManualAttendance)) {
            $automatic = false;
        }


        $companyID = current_companyID();
        $current_userID = current_userID();

        if ($this->form_validation->run() == FALSE) {
            exit(json_encode(['e', validation_errors()]));
        }

        $startdate = $this->input->post('upload_fromDate');
        $enddate = $this->input->post('upload_toDate');

        if ($automatic) {
            $attTempTableQuery = "select * from `srp_erp_pay_empattendancetemptable` WHERE companyID=$companyID AND isUpdated=0 AND attDate between '{$startdate}' AND '{$enddate}' ";
            $attTempData = $this->db->query($attTempTableQuery)->result_array();
            if (empty($attTempData)) {
                echo json_encode(['e', 'No Records Found.']);
                exit;
            }
        }

        $this->db->trans_start();

        /*here*/
        $begin = new DateTime($this->input->post('upload_fromDate'));
        $end = new DateTime($this->input->post('upload_toDate'));

        $end = $end->add(new DateInterval('P1D'));

        $dateRange = new DatePeriod($begin, new DateInterval('P1D'), $end);

        $companyID = current_companyID();
        $frmDate = $this->input->post('upload_fromDate');
        $toDate = $this->input->post('upload_toDate');
        $uniqueKey = current_userID() . '' . current_companyID() . '' . rand(2, 500) . '' . date('YmdHis');
        $date_arr = [];
        foreach ($dateRange as $key => $date) {
            $date_arr[$key]['actualDate'] = $date->format("Y-m-d");
            $date_arr[$key]['uniqueID'] = $uniqueKey;
            $date_arr[$key]['companyID'] = $companyID;

        }

        if (!$automatic) {


            if (count($date_arr) != 1) {
                echo json_encode(['e', 'You can only pull data within a date for manual attendance.']);
                exit;
            }
        }

        if (!empty($date_arr)) {
            $this->db->insert_batch('srp_erp_pay_empattendancedaterangetemp', $date_arr);
        }

        $sql = "select * FROM (SELECT temp.autoID, EIdNo, ECode, Ename1, dateRange.actualDate, temp.empMachineID, temp.attDate, temp.attTime AS attTime, shiftDet.onDutyTime AS onDutyTime, shiftDet.offDutyTime AS offDutyTime, shiftDet.isWeekend, floorDescription, employee.floorID, IF ( IFNULL(leaveMasterID, 0), 1, 0 ) AS isOnLeave, IF (IFNULL(holiday_flag, 0), 1, 0) AS holiday, attDateTime, employee.isCheckin, IF (IFNULL(isHalfDay, 0), 1, 0) AS isHalfDay FROM srp_employeesdetails AS employee INNER JOIN ( SELECT actualDate, companyID FROM `srp_erp_pay_empattendancedaterangetemp` WHERE companyID = {$companyID} AND uniqueID = '{$uniqueKey}' ) AS dateRange ON dateRange.companyID = Erp_companyID LEFT JOIN ( SELECT floorID, floorDescription FROM srp_erp_pay_floormaster WHERE companyID = {$companyID} ) fd ON fd.floorID = employee.floorID LEFT JOIN ( SELECT srp_erp_pay_empattendancetemptable.autoID, empMachineID, attDate, attTime, attDateTime FROM srp_erp_pay_empattendancetemptable WHERE companyID = {$companyID} AND ( attDate BETWEEN '{$frmDate}' AND '{$toDate}' ) AND isUpdated = 0 ) temp ON temp.empMachineID = employee.empMachineID AND actualDate = attDate LEFT JOIN ( SELECT * FROM srp_erp_pay_shiftemployees WHERE companyID = {$companyID} ) AS empShift ON empShift.empID = employee.EIdNo LEFT JOIN ( SELECT * FROM srp_erp_pay_shiftdetails WHERE companyID = {$companyID} ) AS shiftDet ON shiftDet.shiftID = empShift.shiftID AND shiftDet.weekDayNo = WEEKDAY(dateRange.actualDate) LEFT JOIN ( SELECT * FROM srp_erp_calender WHERE companyID = {$companyID} ) AS calenders ON fulldate = dateRange.actualDate LEFT JOIN ( SELECT leaveMasterID, empID, startDate, endDate FROM srp_erp_leavemaster WHERE companyID = {$companyID} AND approvedYN = 1 ) AS leaveExist ON leaveExist.empID = employee.EIdNo AND dateRange.actualDate BETWEEN leaveExist.startDate AND leaveExist.endDate WHERE Erp_companyID = {$companyID} AND isSystemAdmin <> 1 AND employee.floorID = $floorID GROUP BY actualDate, attDateTime, EIdNo ASC ORDER BY EIdNo, actualDate, temp.autoID ASC )temp LEFT JOIN (SELECT empID,attendanceDate FROM `srp_erp_pay_empattendancereview` WHERE  companyID={$companyID} group by 	empID,attendanceDate ) review on  EIdNo=empID AND review.attendanceDate=temp.actualDate WHERE empID is null";


        $temp = $this->db->query($sql)->result_array();
        $tempAttendanceDate = array_column($temp, 'autoID');
        $tempAttendanceArray = array_unique(array_filter($tempAttendanceDate));
        if (!empty($tempAttendanceArray)) {
            foreach ($tempAttendanceArray as $key => $item) {
                $tempattendaceUpdate[$key]['autoID'] = $item;
                $tempattendaceUpdate[$key]['isUpdated'] = 1;
            }

        }
        if (!empty($tempattendaceUpdate)) {
            $this->db->update_batch('`srp_erp_pay_empattendancetemptable` ', $tempattendaceUpdate, 'autoID');
        }


        $data = [];
        if ($temp) {
            $i = 0;
            $employee = '';
            $continue = FALSE;
            foreach ($temp as $row) {
                if ($continue) {
                    /*skip if its set for clockout */
                    $continue = FALSE;
                    $i++;
                    continue;
                }
                $employee = $row['EIdNo'];
                $attendanceDate = $row['actualDate'];
                $onDuty = $row['onDutyTime'];
                $offDuty = $row['offDutyTime'];
                $clockIn = $row['attTime'];
                $nextKey = $i + 1;
                $clockOut = NULL;
                $isAllSet = 0;
                $earlyHours = '';
                $lateHours = '';
                $workingHours = "";
                $totWorkingHours = "";
                $realtime = "";
                $overTimeHours = '';
                $actualWorkingHours_obj = NULL;
                $totWorkingHours_obj = NULL;
                $normaloverTimeHours = 0;
                $weekendOTHours = 0;
                $holidayoverTimeHours = 0;
                $isCheckin = 0;
                $isHalfDay = 1;
                $normalrealtime = 0;
                $weekendrealtime = 0;
                $holidayrealtime = 0;

                /*check next array */
                if (array_key_exists($nextKey, $temp)) {
                    if ($temp[$nextKey]['EIdNo'] == $row['EIdNo'] && $attendanceDate == $temp[$nextKey]['actualDate']) {
                        $clockOut = $temp[$nextKey]['attTime']; /*Set clockout*/
                        $continue = TRUE;
                    }

                }

                /************ Calculate the actual working hours *************/
                if ($onDuty != NULL && $offDuty != NULL && $clockOut != NULL) {
                    $datetime1 = new DateTime($onDuty);
                    $datetime2 = new DateTime($offDuty);
                    $actualWorkingHours_obj = $datetime1->diff($datetime2);
                    $minutes = $actualWorkingHours_obj->format('%i');
                    $hours = $actualWorkingHours_obj->format('%h');
                    $workingHours = ($hours * 60) + $minutes;
                } else {
                    $isAllSet += 1;
                }


                /****** Employee total working hours for this day ******/
                if ($clockIn != NULL && $clockOut != NULL) {

                    if ($offDuty != '' && $offDuty <= $clockOut) {
                        $datetime1 = new DateTime($offDuty);
                    } else {
                        $datetime1 = new DateTime($clockOut);
                    }
                    if ($onDuty != '' && $onDuty >= $clockIn) {
                        $datetime2 = new DateTime($onDuty);
                    } else {
                        $datetime2 = new DateTime($clockIn);
                    }
                    $totWorkingHours_obj = $datetime1->diff($datetime2);
                    $Hours = $totWorkingHours_obj->format('%h');
                    $minutes = $totWorkingHours_obj->format('%i');
                    $totWorkingHours = ($Hours * 60) + $minutes;

                    if ($workingHours != "" && $totWorkingHours != "") {
                        $realtime = $totWorkingHours / $workingHours;
                        $realtime = round($realtime, 1);
                    }


                } else {
                    $isAllSet += 1;
                }


                if ($isAllSet == 0) {

                    /**** Calculation for late hours ****/
                    $clockIn_datetime = new DateTime($clockIn);
                    $onDuty_datetime = new DateTime($onDuty);
                    if ($clockIn_datetime->format('H:i:s') > $onDuty_datetime->format('H:i:s')) {
                        $interval = $clockIn_datetime->diff($onDuty_datetime);

                        $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
                        $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
                        $lateHours = $hours * 60 + $minutes;
                    }


                    /**** Calculation for early hours ****/
                    $datetime1 = date('Y-m-d H:i:s', strtotime($clockOut));
                    $datetime2 = date('Y-m-d H:i:s', strtotime($offDuty));
                    if ($datetime1 < $datetime2) {
                        $datetime1 = new DateTime($clockOut);
                        $datetime2 = new DateTime($offDuty);
                        $interval = $datetime2->diff($datetime1);
                        $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
                        $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
                        $earlyHours = $hours * 60 + $minutes;

                    }


                    $clockouttime = date('Y-m-d H:i:s', strtotime($clockOut));
                    $offduty = date('Y-m-d H:i:s', strtotime($offDuty));

                    if ($clockouttime > $offduty) {


                        $Fdate = date('Y-m-d');
                        $onDutyForOT = new DateTime($clockOut);
                        if ($onDuty >= $clockIn) {
                            $onDutyForOT = new DateTime($onDuty);
                        } else {
                            $onDutyForOT = new DateTime($clockIn);
                        }
                        $clockOutForOT = new DateTime($offDuty);
                        $workingHours_obj = $onDutyForOT->diff($clockOutForOT);
                        $totW = new DateTime($workingHours_obj->format('' . $Fdate . ' %h:%i:%s'));
                        $actW = new DateTime($actualWorkingHours_obj->format('' . $Fdate . ' %h:%i:%s'));
                        $overTime_obj = $actW->diff($totW);
                        $hours = ($overTime_obj->format('%h') != 0) ? $overTime_obj->format('%h') : 0;
                        $minutes = ($overTime_obj->format('%i') != 0) ? $overTime_obj->format('%i') : 0;
                        $overTimeHours = $hours * 60 + $minutes;
                    }


                }


                if ($clockIn == NULL && $clockOut == NULL) {
                    $AttPresentTypeID = 4;
                    /**** Absents *****/
                } else {
                    $clockIn_datetime = date('Y-m-d H:i:s', strtotime($clockIn));
                    $onDuty_datetime = date('Y-m-d H:i:s', strtotime($onDuty));
                    if ($clockIn_datetime <= $onDuty_datetime) {
                        $AttPresentTypeID = 1;
                    } /**** Presented On time *****/
                    elseif ($clockIn_datetime > $onDuty_datetime) {
                        $AttPresentTypeID = 2;
                    } /**** Presented Later*****/
                    else {
                        $AttPresentTypeID = '';
                    }
                    /***** Let the user decide ****/
                }

                if ($row['isOnLeave'] == 1) {
                    $AttPresentTypeID = 5;
                }
                /**** Employee On Leave *****/


                $normaloverTimeHours = $overTimeHours;
                $normalrealtime = $realtime;
                $isNormalDay = 0;
                $isWeekEndDay = 0;
                $isHoliday = 0;


                if ($row['isWeekend'] == 1) {
                    /**/
                    if ($clockIn != NULL || $clockOut != NULL) {
                        $AttPresentTypeID = 1;
                    }
                    $overTimeHours = $totWorkingHours;
                    /**/
                    $normaloverTimeHours = 0;
                    $weekendOTHours = $totWorkingHours;

                    $normalrealtime = 0;
                    $weekendrealtime = $realtime;
                    $isNormalDay = 0;
                    $isWeekEndDay = 1;


                }

                if ($row['holiday'] == 1) {
                    /**/
                    if ($clockIn != NULL || $clockOut != NULL) {
                        $AttPresentTypeID = 1;
                    }
                    $overTimeHours = $totWorkingHours;
                    /**/
                    $normaloverTimeHours = 0;
                    $weekendOTHours = 0;
                    $holidayoverTimeHours = $totWorkingHours;
                    $normalrealtime = 0;
                    $weekendrealtime = 0;
                    $holidayrealtime = $realtime;
                    $isNormalDay = 0;
                    $isWeekEndDay = 0;
                    $isHoliday = 1;
                }

                if ($row['isCheckin'] == 1) {
                    $isCheckin = 1;
                }

                if ($row['isHalfDay'] == 1) {
                    $isHalfDay = 0.5;
                }


                array_push($data, [
                    'empID' => $row['EIdNo'],
                    'machineID' => ($row['empMachineID'] != '' ? $row['empMachineID'] : 0),
                    'floorID' => $floorID,//$row['floorID'],
                    'attendanceDate' => $attendanceDate,
                    'onDuty' => $onDuty,
                    'offDuty' => $offDuty,
                    'checkIn' => $clockIn,
                    'checkOut' => $clockOut,
                    'presentTypeID' => $AttPresentTypeID,
                    'lateHours' => $lateHours,
                    'earlyHours' => $earlyHours,
                    'OTHours' => $overTimeHours,
                    'weekendOTHours' => $weekendOTHours,
                    'mustCheck' => $isCheckin,
                    'normalTime' => $isHalfDay,
                    'realTime' => $realtime,
                    'NDaysOT' => $normaloverTimeHours,
                    'holidayOTHours' => $holidayoverTimeHours,
                    'normalDay' => $normalrealtime,
                    'weekend' => $weekendrealtime,
                    'holiday' => $holidayrealtime,
                    'companyID' => current_companyID(),
                    'companyCode' => current_companyCode(),
                    'isNormalDay' => $isNormalDay,
                    'isWeekEndDay' => $isWeekEndDay,
                    'isHoliday' => $isHoliday,

                ]);


                $i++;
            }

        }
        //$this->db->where('uniqueID', $uniqueKey)->delete('srp_erp_pay_empattendancedaterangetemp');

        if (!empty($data)) {

            $this->db->insert_batch('srp_erp_pay_empattendancereview', $data);
        } else {
            echo json_encode(['e', 'No records found']);
            exit;
        }

        /* exit;*/
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(['e', 'Failed to Update ']);
        } else {
            $this->db->trans_commit();
            echo json_encode(['s', 'Successfully uploaded']);
        }


    }

    function load_empAttDataView()
    {
        $this->form_validation->set_rules('fromDate', 'From Date', 'required|date');
        $this->form_validation->set_rules('toDate', 'To Date', 'required|date');
        $this->form_validation->set_rules('floorID[]', 'Floor ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $begin = new DateTime($this->input->post('fromDate'));
            $end = new DateTime($this->input->post('toDate'));
            $enddiff = $end->add(new DateInterval('P1D'));

            $dateRange = new DatePeriod($begin, new DateInterval('P1D'), $enddiff);

            $date_arr = array();
            foreach ($dateRange as $key => $date) {
                $date_arr[$date->format("Y-m-d")] = $date->format("Y-m-d");
            }

            $data['tempAttData'] = $this->Employee_model->get_attendanceData2();


            echo json_encode(
                array(
                    '0' => 's',
                    'tBody' => $this->load->view('system/hrm/ajax/load_empAttendanceReviewTBody', $data, true),
                    'rowCount' => count($data['tempAttData']),
                    'date_arr' => $date_arr,
                    'unAssignedShifts' => $this->Employee_model->getShift_notAssignedEmployees(),
                    'unAssignedMachineID' => $this->Employee_model->getMachineID_notAssignedEmployees($this->input->post('fromDate'), $this->input->post('toDate'))
                )
            );
        }
    }

    function save_attendanceReviewData()
    {
        //echo ' < pre>';print_r($_POST); echo ' </pre > '; die();
        echo json_encode($this->Employee_model->save_attendanceReviewData());
    }

    function getData()
    {

        $this->form_validation->set_rules('fromDate', 'From Date', 'required|date');
        $this->form_validation->set_rules('toDate', 'To Date', 'required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            echo json_encode(
                array(
                    '0' => 's',
                    'tempAttData' => $this->Employee_model->get_attendanceData(),
                    'unAssignedShifts' => $this->Employee_model->getShift_notAssignedEmployees(),
                    'unAssignedMachineID' => $this->Employee_model->getMachineID_notAssignedEmployees()
                )
            );
        }
    }

    function load_empAttData()
    {
        $data['attData'] = array(
            '0' => 's',
            'tempAttData' => $this->Employee_model->get_attendanceData(),
            'unAssignedShifts' => $this->Employee_model->getShift_notAssignedEmployees(),
            'unAssignedMachineID' => $this->Employee_model->getMachineID_notAssignedEmployees(),
        );

        $this->load->view('system/hrm/ajax/load_empAttemdanceReview', $data);
    }

    /*End of Attendance review*/

    function fetch_employees_typeAhead()
    {
        $result = $this->Employee_model->fetch_employees_typeAhead();
        echo json_encode($result);
    }

    function save_social_insurance()
    {
        $this->form_validation->set_rules('socialInsuranceMasterID', 'Social Insurance', 'required');
        $this->form_validation->set_rules('socialInsuranceNumber', 'Social Insurance No', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $result = $this->Employee_model->save_social_insurance();
            echo json_encode($result);
        }
    }


    function fetch_socialInsurance()
    {
        $empId = trim($this->input->post('empID'));

        $base_url = site_url('Employee/update_si');

        $this->datatables->select('if(t2.Description is NULL,t3.Description,t2.Description) as Description,t1.socialInsuranceDetailID AS socialInsuranceDetailID, t1.socialInsuranceNumber AS socialInsuranceNumber,t2.employeeContribution as employeecontribution,t2.employerContribution as employercontribution', false)
            ->from('srp_erp_socialinsurancedetails t1')
            ->join('srp_erp_socialinsurancemaster AS t2', 't1.socialInsuranceMasterID = t2.socialInsuranceID', 'left')
            ->join('srp_erp_payeemaster AS t3', 't1.payeeID = t3.payeeMasterID', 'left')
            ->where('t1.empID', $empId)
            ->where('t1.companyID', current_companyID())
            ->add_column('socialNumber', '<a class="socialNumber" id="socialNumber_$2" data-type="text" data-pk="$2"  data-url ="' . $base_url . '" data-title="Social Number">$1</a> ', 'socialInsuranceNumber,socialInsuranceDetailID')
            ->add_column('contribution', '$1', 'load_employee_contribution(employeecontribution,employercontribution)')
            ->add_column('delete', ' <span class="pull-right"><a onclick="delete_si(\'$1\')" class="atagdsabl"><span class="glyphicon glyphicon-trash" style="color:#d15b47;" ></span></a></span> ', 'socialInsuranceDetailID');
        echo $this->datatables->generate();
    }

    function delete_si()
    {
        $this->form_validation->set_rules('socialInsuranceDetailID', 'Social Insurance', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $result = $this->Employee_model->delete_si();
            echo json_encode($result);
        }
    }

    function update_si()
    {
        $this->form_validation->set_rules('pk', 'Document Id is Missing', 'required');
        $this->form_validation->set_rules('value', 'Socail Number is required . ', 'required');
        $this->form_validation->set_rules('empId', 'Employee Id is Missing', 'required');


        if ($this->form_validation->run() == FALSE) {
            return $this->output
                ->set_content_type('application/html')
                ->set_status_header(400)
                ->set_output(validation_errors());
        } else {
            $result = $this->Employee_model->update_si();
            echo $result;
        }
    }

    /*Nationality*/
    public function fetch_nationality()
    {
        $this->datatables->select('NId,Nationality, IFNULL((SELECT COUNT(EIdNo) FROM srp_employeesdetails WHERE NId = t1.NId GROUP BY Nid), 0 ) AS usageCount')
            ->from('srp_nationality AS t1')
            ->add_column('edit', '$1', 'action_nationality(NId, Nationality, usageCount)')
            ->where('Erp_companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function saveNationality()
    {
        $this->form_validation->set_rules('description[]', 'Nationality', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->saveNationality());
        }
    }

    public function editNationality()
    {
        $this->form_validation->set_rules('nationalityDes', 'Nationality', 'required');
        $this->form_validation->set_rules('hidden-id', 'Nationality ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->editNationality());
        }
    }

    public function deleteNationality()
    {
        $this->form_validation->set_rules('hidden-id', 'Nationality ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->deleteNationality());
        }
    }

    /*Social Insurance*/
    function fetch_social_insurance()
    {
        $companyID = current_companyID();
        $this->datatables->select("socialInsuranceID,Description, IFNULL((SELECT COUNT(socialInsuranceDetailID) FROM srp_erp_socialinsurancedetails WHERE socialInsuranceMasterID=t1.socialInsuranceID AND companyID='{$companyID}' GROUP BY socialInsuranceMasterID), 0) AS usageCount,employeeContribution AS employeeContribution,employerContribution AS employerContribution,sortCode,GLtbl1.GLSecondaryCode AS expenceGlCode,GLtbl2.GLSecondaryCode AS liablityGlCOde,expenseGlAutoID AS expenseGlAutoID,liabilityGlAutoID AS liabilityGlAutoID,isSlabApplicable AS isSlabApplicable,SlabID AS SlabID")
            ->from('srp_erp_socialinsurancemaster AS t1')
            ->join('srp_erp_chartofaccounts GLtbl1', 't1.expenseGlAutoID = GLtbl1.GLAutoID', 'left')
            ->join('srp_erp_chartofaccounts GLtbl2', 't1.liabilityGlAutoID = GLtbl2.GLAutoID', 'left')
            ->add_column('edit', '$1', 'action_social_insurance(socialInsuranceID, Description, employeeContribution, employerContribution, sortCode, usageCount, expenseGlAutoID, liabilityGlAutoID, isSlabApplicable, SlabID)')
            ->edit_column('employeeContribution', '$1%', 'employeeContribution')
            ->edit_column('employerContribution', '$1%', 'employerContribution')
            ->where('t1.companyID', $companyID);

        echo $this->datatables->generate();
    }

    function saveSocialInsurance()
    {
        $this->form_validation->set_rules('sortCode[]', 'Sort Code', 'trim|required');
        $this->form_validation->set_rules('description[]', 'Description', 'trim|required');
        $this->form_validation->set_rules('employee[]', 'Employee Contribution', 'trim|required');
        $this->form_validation->set_rules('employer[]', 'Employer Contribution', 'trim|required');
        $this->form_validation->set_rules('liabilityGlAutoID[]', 'Liability GL Code', 'trim|required');

        $employees = $this->input->post('employee');
        $employers = $this->input->post('employer');
        $expenseGlAutoID = $this->input->post('expenseGlAutoID');
        $isSlabApplicable = $this->input->post('isSlabHidden');
        $slabID = $this->input->post('ifSlab');

        $validateExpenseGL = false;
        $expenseGLBlank = false;
        $validateSlabID = false;

        foreach ($employers as $key => $employer) {
            if ($employer > 0 AND $expenseGlAutoID[$key] == '') {
                $validateExpenseGL = true;
            }

            if ($employees[$key] > 0 AND $expenseGlAutoID[$key] !== '') {
                $expenseGLBlank = true;
            }

            if ($isSlabApplicable[$key] == 1) {
                if ($slabID[$key] == '') {
                    $validateSlabID = true;
                }
            }
        }


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if ($validateExpenseGL) {
                die(json_encode(['e', 'The Expense GL Code field is required for employer contribution']));
            }


            if ($expenseGLBlank) {
                die(json_encode(['e', 'You can not select expense GL code for employee contribution']));
            }

            if ($validateSlabID == true) {
                die(json_encode(['e', 'If slab is applicable slab ID can not be blank.']));
            }

            echo json_encode($this->Employee_model->saveSocialInsurance());
        }
    }

    function editSocialInsurance()
    {
        $this->form_validation->set_rules('siSortCode', 'Social Insurance Sort Code', 'required');
        $this->form_validation->set_rules('siDes', 'Social Insurance Description', 'required');

        $this->form_validation->set_rules('si_liabilityGlAutoID', 'Liability GL Code', 'required');
        $this->form_validation->set_rules('hidden-id', 'Social Insurance ID', 'required');
        $this->form_validation->set_rules('siEmployee', 'Employee Contribution', 'required');
        $this->form_validation->set_rules('siEmployer', 'Employer Contribution', 'required');

        $employer = (int)trim($this->input->post('siEmployer'));

        if ($employer > 0) {
            $this->form_validation->set_rules('si_expenseGlAutoID', 'Expense GL Code', 'required');
        }


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $employee = (int)trim($this->input->post('siEmployee'));
            $expenseGlAutoID = (int)trim($this->input->post('si_expenseGlAutoID'));

            if ($expenseGlAutoID != '' && $employee > 0) {
                die(json_encode(['e', 'You can not select expense GL code for employee contribution']));
            }

            $isSlabApplicable = trim($this->input->post('siIsSlab'));
            if ($isSlabApplicable == true) {
                $slabID = trim($this->input->post('siSlab'));
                if (empty($slabID)) {
                    die(json_encode(['e', 'If slab is applicable slab can not be blank.']));
                }

            }

            echo json_encode($this->Employee_model->editSocialInsurance());
        }
    }

    function deleteSocialInsurance()
    {
        $this->form_validation->set_rules('hidden-id', 'Social Insurance ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->deleteSocialInsurance());
        }
    }

    function get_usermanagement()
    {
        $empID = $this->input->post('empID');
        $filter = '';
        if ($empID != 'All') {
            $filter = "AND EidNo ='{$empID}'";
        }
        $companyID = current_companyID();

        $data = $this->db->query("SELECT * FROM `srp_employeesdetails` WHERE `Erp_companyID` = '{$companyID}' $filter")->result_array();

        echo json_encode($data);
    }

    function updateEmployeeDetails()
    {
        $arr = array();
        $arr2 = array();
        $empArr = $this->input->post('empID');
        $password = $this->input->post('password');
        $isActive = $this->input->post('isActive');
        $companyID = current_companyID();
        $data = $this->db->query("SELECT * FROM `srp_employeesdetails` WHERE `Erp_companyID` = '{$companyID}'")->result_array();

        for ($x = 0; $x <= count($data) - 1; $x++) {
            if ($empArr) {
                for ($i = 0; $i <= count($empArr) - 1; $i++) {
                    if ($data[$x]['EIdNo'] == $empArr[$i]) {
                        if ($password[$i] != "***********") {
                            $psw = md5($password[$i]);
                        } else {
                            $psw = $data[$x]['Password'];
                        }
                        array_push($arr, array('EIdNo' => $data[$x]['EIdNo'], 'Password' => $psw, 'isActive' => $isActive[$i]));
                        array_push($arr2, array('Username' => $data[$x]['UserName'], 'Password' => $psw));
                    }


                }
            }
        }
        if (!empty($arr)) {
            $this->db->update_batch('srp_employeesdetails', $arr, 'EIdNo');
            $db = $this->load->database('db2', true);
            $db->update_batch('user', $arr2, 'Username');
            $this->session->set_flashdata('s', 'Successfully updated');
            echo json_encode(false);
        } else {
            $this->session->set_flashdata('e', 'No changes found');
            echo json_encode(false);
        }
        exit;

    }

    function fetch_declaration_employees_master()
    {
        $companyID = current_companyID();
        $isGroupAccess = getPolicyValues('PAC', 'All');
        $convertFormat = convert_date_format_sql();

        $this->datatables->select("salarydeclarationMasterID,Description,documentSystemCode,transactionCurrency,confirmedYN,approvedYN,createdUserID,
        DATE_FORMAT(documentDate,' $convertFormat ') AS newDocumentDate,transactionCurrency,Description");
        $this->datatables->from('srp_erp_salarydeclarationmaster');
        $this->datatables->where('companyID', $companyID);
        $this->datatables->add_column('confirmed', '$1', 'confirm(confirmedYN)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SD",salarydeclarationMasterID)');
        $this->datatables->add_column('edit', '$1', 'load_salary_reconsilation_action(salarydeclarationMasterID, confirmedYN, approvedYN, createdUserID)');

        if($isGroupAccess == 1){
            $currentEmp = current_userID();
            // Usage of UNION in this sub query
            // to get the declaration master record that are not contain any record in detail table record
            // which means we can not get the access rights with out a employee in detail table

            $this->datatables->join("(SELECT decID FROM srp_erp_payrollgroupincharge AS inCharge
                                      JOIN (
                                            SELECT declarationMasterID AS decID, accessGroupID
                                            FROM srp_erp_salarydeclarationdetails
                                            WHERE companyID={$companyID} AND accessGroupID IS NOT NULL
                                            GROUP BY declarationMasterID, accessGroupID
                                      ) AS declrationTB ON inCharge.groupID=declrationTB.accessGroupID
                                      WHERE companyID={$companyID} AND empID={$currentEmp}
                                      GROUP BY decID
                                      UNION
                                          SELECT salarydeclarationMasterID
                                          FROM srp_erp_salarydeclarationmaster AS t1
                                          LEFT JOIN srp_erp_salarydeclarationdetails AS t2
                                          ON t2.declarationMasterID=t1.salarydeclarationMasterID
                                          WHERE t1.companyID={$companyID} AND declarationMasterID IS NULL
                                          GROUP BY t1.salarydeclarationMasterID
                                      ) AS accTB", 'srp_erp_salarydeclarationmaster.salarydeclarationMasterID = accTB.decID');

        }
        echo $this->datatables->generate();
    }

    function save_employee_declaration_master()
    {
        $this->form_validation->set_rules('MasterCurrency', 'Currency', 'trim|required');
        $this->form_validation->set_rules('salary_description', 'Description', 'trim|required');
        $this->form_validation->set_rules('documentDate', 'Document date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_employee_declaration_master());
        }
    }

    /*Load salary declaration detail after adding master */
    function Load_Salary_Declaration_Master()
    {
        $id = $this->input->post('id');
        $result = $this->Employee_model->get_salaryDeclarationMaster($id);

        $data['balancePayment'] = $this->db->query("SELECT * FROM `srp_erp_pay_balancepayment` WHERE sdMasterID={$id}")->result_array();
        if (!empty($result)) {
            $data['output'] = $result;
            echo $this->load->view('system/hrm/employee_salary_declaration_detail', $data, true);

        } else {
            return false;
        }
    }

    function getSalarySubType()
    {
        $masterCat = trim($this->input->post('masterCategory'));
        $isPayrollCategory = trim($this->input->post('isPayrollCategoryYN'));
        $isFromMA = trim($this->input->post('isFromMA-D'));
        $companyID = current_companyID();

        if ($isFromMA == 'Y') {
            $Categories = $this->db->query("SELECT * FROM srp_erp_pay_salarycategories WHERE isPayrollCategory={$isPayrollCategory}
                                            AND companyID ={$companyID}")->result_array();
        } else {
            $Categories = $this->db->query("SELECT * FROM srp_erp_pay_salarycategories WHERE salaryCategoryType='{$masterCat}'
                                            AND isPayrollCategory ={$isPayrollCategory} AND companyID ={$companyID}")->result_array();
        }


        $Categories_arr = '';
        if (isset($Categories)) {
            foreach ($Categories as $row) {
                $Categories_arr[trim($row['salaryCategoryID'])] = trim($row['salaryDescription']);
            }
        }
        $option = '<option value=""> Select Fixed Allowance </option>';
        foreach ($Categories_arr as $key => $Categoriy) {
            $option .= "<option value='{$key}'>{$Categoriy}</option>";
        }
        //echo $this->db->last_query().'</p>';
        echo $option;
    }

    function save_salary_declaration_detail()
    {

        $cat = $this->input->post('cat');
        $effDate = $this->input->post('effectiveDate');
        $masterID = trim($this->input->post('declarationMasterID'));

        $result = $this->Employee_model->get_salaryDeclarationMaster($masterID);
        $isPayrollCategory = $result['isPayrollCategory'];
        $this->load->helper('template_paySheet_helper');
        $errorCount = 0;
        $errMsg = '';

        /*  foreach ($cat as $key => $catVal) {
              $isPayrollProcessed = isPayrollProcessed($effDate, $isPayrollCategory);

              if ($isPayrollProcessed['status'] == 'Y') {
                  $errorCount++;
                  $greaterThanDate = date('Y-F', strtotime($isPayrollProcessed['year'] . '-' . $isPayrollProcessed['month'] . '-01'));
                  $errMsg = 'Effective date should be greater than [' . $greaterThanDate . '] <p> for all salary  declarations';
              }
          }*/

        if ($errorCount == 0) {
            echo json_encode($this->Employee_model->save_all_salary_declaration());
        } else {
            echo json_encode(array('e', $errMsg));
        }


    }

    function getEmployeesDeclaration($currency)
    {
        $companyID = current_companyID();
        $employees = $this->db->query("SELECT EIdNo, ECode, Ename2, EDOJ FROM srp_employeesdetails WHERE Erp_companyID={$companyID} AND payCurrencyID={$currency}
                                        AND isActive = 1 AND isPayrollEmployee=1 AND empConfirmedYN=1 AND isDischarged=0")->result_array();
        return $employees;
    }

    function ConfirmSalaryDeclaration()
    {
        echo json_encode($this->Employee_model->ConfirmSalaryDeclaration());
    }

    function fetch_salary_declaration_approval()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */
        $approvedYN = trim($this->input->post('approvedYN'));
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('salarydeclarationMasterID AS masterID, srp_erp_salarydeclarationmaster.companyCode,Description,transactionCurrency,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID,approvalLevelID,DATE_FORMAT(srp_erp_salarydeclarationmaster.documentDate,\'' . $convertFormat . '\') AS newDocumentDate, srp_erp_salarydeclarationmaster.documentSystemCode AS docCode', false);
        $this->datatables->from('srp_erp_salarydeclarationmaster');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_salarydeclarationmaster.salarydeclarationMasterID AND srp_erp_documentapproved.approvalLevelID = srp_erp_salarydeclarationmaster.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_salarydeclarationmaster.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'SD');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'SD');
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_salarydeclarationmaster.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN')));
        $this->datatables->add_column('purchaseOrderCode', '$1', 'approval_change_modal(purchaseOrderCode, purchaseOrderID, documentApprovedID, approvalLevelID, approvedYN, PO,0)');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('docCode', '$1', 'load_salary_action_approval(masterID, approvalLevelID, approvedYN, documentApprovedID, \'code\', docCode)');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SD", masterID)');
        $this->datatables->add_column('edit', '$1', 'load_salary_action_approval(masterID, approvalLevelID, approvedYN, documentApprovedID, \'edit\')');

        echo $this->datatables->generate();
    }

    function load_salary_approval_confirmation()
    {

        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('declarationMasterID'));
        $data['extra'] = $this->Employee_model->get_salaryDeclarationMaster($masterID);
        $data['balancePayment'] = $this->db->query("SELECT * FROM `srp_erp_pay_balancepayment` WHERE sdMasterID={$masterID}")->result_array();
        $html = $this->load->view('system/hrm/salary_declaration_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function save_salary_declaration_approval()
    {
        $this->form_validation->set_rules('approval_status', 'Salary Declaration Status', 'trim|required');
        if ($this->input->post('approval_status') == 2) {
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        }
        $this->form_validation->set_rules('salaryOrderID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {
            echo json_encode($this->Employee_model->save_salary_declaration_approval());
        }
    }

    function getDeclarationmasterCurrency_edit()
    {
        echo json_encode($this->Employee_model->getDeclarationmasterCurrency_edit());
    }

    function delete_salary_declaration()
    {
        echo json_encode($this->Employee_model->delete_salary_declaration());
    }

    function save_pay_slabs_master()
    {
        $this->form_validation->set_rules('documentDate', 'Document Date', 'trim|required');
        $this->form_validation->set_rules('MasterCurrency', 'Currency', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_pay_slabs_master());
        }
    }

    function fetch_pay_slab_master()
    {
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("slabsMasterID,Description,documentSystemCode,transactionCurrency,DATE_FORMAT(documentDate,'  $convertFormat ') AS newDocumentDate");
        $this->datatables->from('srp_erp_slabsmaster');
        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('edit', '$1', 'load_salary_slab_action(slabsMasterID)');
        echo $this->datatables->generate();
    }

    /*Load salary declaration detail after adding master */
    function Load_pay_slab_master_detail()
    {
        $id = $this->input->post('id');
        $result = $this->Employee_model->get_paySlabMaster($id);
        if (!empty($result)) {
            $data['output'] = $result;
            echo $this->load->view('system/hrm/pay_slab_detail', $data, true);
        } else {
            return false;
        }
    }


    function save_pay_slabs_detail()
    {
        $companyID = current_companyID();
        $amtStart = trim($this->input->post('start_amount'));
        $amtEnd = trim($this->input->post('end_amount'));
        $masterID = trim($this->input->post('slabMasterID'));

        $errorCount = 0;
        $errMsg = '';

        $slabDetail = $this->db->query("SELECT slabsDetailID,companyID,rangeStartAmount,rangeEndAmount FROM `srp_erp_slabsdetail` WHERE `companyID` = '{$companyID}' AND slabsMasterID = '{$masterID}' order by slabsDetailID desc")->row_array();

        $this->form_validation->set_rules('start_amount', 'Start Range Amount', 'trim|required');
        $this->form_validation->set_rules('end_amount', 'End Range Amount', 'trim|required');
        $this->form_validation->set_rules('percentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('threshold_amount', 'Threshold Amount', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if ($amtStart >= $amtEnd) {
                $errorCount++;
                $errMsg = 'End Range Amount should be greater than Start Range Amount';
            }
            if (!empty($slabDetail)) {
                if ($slabDetail['rangeEndAmount'] >= $amtStart) {
                    $errorCount++;
                    $errMsg = 'Start Range Amount should be greater than last End Range Amount';
                }
            }
            if ($errorCount == 0) {
                echo json_encode($this->Employee_model->save_pay_slabs_detail());
            } else {
                echo json_encode(array('e', $errMsg));
            }
        }
    }

    function delete_payee_slab_detail()
    {
        echo json_encode($this->Employee_model->delete_payee_slab_detail());
    }

    function delete_salary_declaration_master()
    {
        echo json_encode($this->Employee_model->delete_salary_declaration_master());
    }

    function updatePayGroupDetails()
    {
        $this->form_validation->set_rules('salaryCategory[]', 'Please select atleast one', 'required');
        $this->form_validation->set_rules('payGroupId', 'ID is missing', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->updatePayGroupDetails());
        }
    }

    function getSalaryCategories()
    {
        $companyID = current_companyID();
        $socialInsuranceID = $this->input->post('socialInsuranceID');

        $payGroupDetails = $this->db->query("SELECT
srp_erp_paygroupdetails.salaryCategoryID,
srp_erp_paygroupdetails.salaryCategoryID,
srp_erp_paygroupmaster.description,
srp_erp_paygroupmaster.socialInsuranceID,
srp_erp_pay_salarycategories.salaryDescription
FROM
srp_erp_paygroupdetails
INNER JOIN srp_erp_paygroupmaster ON srp_erp_paygroupdetails.groupID = srp_erp_paygroupmaster.payGroupID
INNER JOIN srp_erp_pay_salarycategories ON srp_erp_paygroupdetails.salaryCategoryID = srp_erp_pay_salarycategories.salaryCategoryID
WHERE srp_erp_paygroupdetails.companyID = '{
                    $companyID}' AND socialInsuranceID='{
                    $socialInsuranceID}' ")->result_array();

        $retrun = '';
        foreach ($payGroupDetails as $payGroupDetail) {
            $retrun .= '<a class="btn btn-sm btn-default"
                                                   onclick="appendFormula(\'' . $payGroupDetail['salaryDescription'] . '\',\'' . $payGroupDetail['salaryCategoryID'] . '\',\'' . $payGroupDetail['salaryCategoryID'] . '\')"
                                                   href="#"><strong> ' . $payGroupDetail['salaryDescription'] . ' </strong></a > ';
        }

//        echo $this->db->last_query();
        echo $retrun;
    }

    public function leave_group_master()
    {
        $this->datatables->select('leaveGroupID, description, companyID, policyDescription, isDefault ', false)
            ->from('srp_erp_leavegroup')
            ->join('srp_erp_leavepolicymaster', ' isMonthly=policyMasterID', 'left')
            ->add_column('isDefault', '$1', 'leaveAction(isDefault, default)')
            ->add_column('action', '$1', 'leaveAction(leaveGroupID, ID)')
            ->where('companyID', current_companyID());
        echo $this->datatables->generate();
    }

    public function save_leaveGroup()
    {
        $this->Employee_model->save_leaveGroup();
    }

    public function LeavegroupDetails()
    {
        $masterID = $this->input->post('masterID');

        $data['master'] = $this->db->query("select isMonthly from `srp_erp_leavegroup` WHERE leaveGroupID={$masterID} ")->row_array();
        $data['details'] = $this->db->query("SELECT CONCAT(FLOOR(noOfHourscompleted/60),'h ',MOD(noOfHourscompleted,60),'m') as noOfHourscompleted,CONCAT(FLOOR(noOfHours/60),'h ',MOD(noOfHours,60),'m') as noOfHours,isAllowminus, isCalenderDays ,leaveGroupDetailID, srp_erp_leavegroup.leaveGroupID, noOfDays,srp_erp_leavetype.description,srp_erp_leavetype.leaveTypeID,policyDescription,isCarryForward FROM srp_erp_leavegroup INNER JOIN     srp_erp_leavegroupdetails on srp_erp_leavegroup.leaveGroupID=srp_erp_leavegroupdetails.leaveGroupID LEFT JOIN `srp_erp_leavetype` on srp_erp_leavetype.leaveTypeID=srp_erp_leavegroupdetails.leaveTypeID LEFT JOIN `srp_erp_leavepolicymaster` on srp_erp_leavegroupdetails.policyMasterID=srp_erp_leavepolicymaster.policyMasterID WHERE srp_erp_leavegroup.leaveGroupID = {$masterID}")->result_array();
        $html = $this->load->view('system/hrm/leave_group_details_table', $data, true);
        echo $html;
    }


    public function getleaveGroupheader()
    {
        $masterID = $this->input->post('masterID');
        $master = $this->db->query("select * from srp_erp_leavegroup WHERE leaveGroupID=$masterID ")->row_array();
        echo json_encode($master);

    }

    public function save_leaveGroupdetail()
    {
        $this->form_validation->set_rules('leavepolicyID', 'ID is missing', 'required');
        $this->form_validation->set_rules('leaveTypeID', 'Leave Type', 'required');
        $policyID = $this->input->post('leavepolicyID');
        $isCarryForward = $this->input->post('isCarryForward');
        if ($policyID == 2) {
            $this->form_validation->set_rules('noOfHours', 'No of Hours', 'required');
            $this->form_validation->set_rules('NoOfMinutes', 'No of Hours', 'required');
            $this->form_validation->set_rules('noOfHourscompleted', 'Hours to be completed', 'required');
            $this->form_validation->set_rules('NoOfMinutesompleted', 'Hours to be completed', 'required');

        } else {
            $this->form_validation->set_rules('noOfDays', 'noOfDays', 'required');

            if ($policyID == 1) {

                if ($isCarryForward == 1) {
                    //$this->form_validation->set_rules('carryForwardLimit', 'Carry Forward Limit', 'required');
                }
            }

        }
        $this->form_validation->set_rules('masterID', 'ID is missing', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $leaveTypeID = $this->input->post('leaveTypeID');
            $noOfDays = $this->input->post('noOfDays');
            $masterID = $this->input->post('masterID');
            $isAllowminus = $this->input->post('isAllowminus');
            $isCalenderDays = $this->input->post('isCalenderDays');
            $carryForwardLimit = $this->input->post('carryForwardLimit');

            $row = $this->db->query("select * from srp_erp_leavegroupdetails WHERe leaveGroupID={$masterID} AND leaveTypeID={$leaveTypeID}")->row_array();
            if (!empty($row)) {
                $this->session->set_flashdata('e', 'Record you inserted already exist');
                echo json_encode(array('error' => 1));
                exit;
            }


            $data['isAllowminus'] = $isAllowminus;
            $data['leaveTypeID'] = $leaveTypeID;
            $data['leaveGroupID'] = $masterID;
            $data['policyMasterID'] = $policyID;
            if ($policyID == 2) {
                $hours = $this->input->post('noOfHours');
                $minutes = $this->input->post('NoOfMinutes');
                $totalMinutes = $hours * 60 + $minutes;
                $data['noOfHours'] = $totalMinutes;

                $hours2 = $this->input->post('noOfHourscompleted');
                $minutes2 = $this->input->post('NoOfMinutesompleted');
                $totalMinutes2 = $hours2 * 60 + $minutes2;
                $data['noOfHourscompleted'] = $totalMinutes2;
            } else {
                $data['noOfDays'] = $noOfDays;
                $data['isCalenderDays'] = $isCalenderDays;

                if ($isCarryForward == 1) {
                    ////$data['carryForwardLimit'] = $carryForwardLimit;
                    $data['isCarryForward'] = 1;
                }
            }
            $insert = $this->db->insert('srp_erp_leavegroupdetails', $data);
            if ($insert) {
                $this->session->set_flashdata('s', 'Successfully Inserted . ');
                echo json_encode(array('error' => 0));
                exit;
            } else {
                $this->session->set_flashdata('e', 'Failed . ');
                echo json_encode(array('error' => 1));
                exit;
            }
        }

    }

    function referback_salary_declaration()
    {
        $masterID = $this->input->post('masterID');
        $masterDetail = $this->Employee_model->get_salaryDeclarationMaster($masterID);

        if ($masterDetail['approvedYN'] == 1) {
            echo json_encode(array('e', 'This document is already approved.<p>You can not refer back this.'));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($masterID, 'SD');
            if ($status == 1) {
                echo json_encode(array('s', $masterDetail['documentSystemCode'] . ' Referred Back Successfully.'));
            } else {
                echo json_encode(array('e', $masterDetail['documentSystemCode'] . ' Error in refer back.'));
            }
        }
    }

    public function get_hrPeriodMonth()
    {
        $hrPeriodID = $this->input->post('hrPeriodID');

        echo form_dropdown('hrPeriodMonth', hrPeriodMonth_drop($hrPeriodID), '', 'class="form-control select2" id="hrPeriodMonth""');
    }


    public function save_leaveAccrual()
    {
        $this->Employee_model->save_leaveAccrual();
    }


    function Load_slab_start_amount()
    {
        $companyID = current_companyID();
        $masterID = $this->input->post('masterID');

        $slabDetail = $this->db->query("SELECT slabsDetailID,companyID,rangeStartAmount,rangeEndAmount FROM srp_erp_slabsdetail
                                        WHERE companyID = '{$companyID}' AND slabsMasterID = '{$masterID}'
                                        ORDER BY slabsDetailID DESC")->row_array();
        echo json_encode($slabDetail);
    }

    function saveFormula()
    {
        $this->form_validation->set_rules('formulaOriginal', 'Formula', 'trim|required');
        $this->form_validation->set_rules('formulaText', 'Formula', 'trim|required');
        $this->form_validation->set_rules('payGroupID', 'ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->saveFormula());
        }
    }

    function saveFormula_new()
    {
        $this->form_validation->set_rules('payGroupID', 'ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->saveFormula_new());
        }
    }

    function formulaDecode()
    {
        $payGroupID = $this->input->post('payGroupID');
        $decodeType = $this->uri->segment(3);
        $companyID = current_companyID();

        if ($decodeType == 'isSalaryComparison') {
            $formula = $this->db->select('formulaStr')->from('srp_erp_salarycomparisonformula')
                ->where('masterID', $payGroupID)->where('companyID', $companyID)->get()->row('formulaStr');

        } else if ($decodeType == 'isSSO_slab') {
            $formula = $this->db->select('formulaString')->from('srp_erp_ssoslabdetails')
                ->where('ssoSlabDetailID', $payGroupID)->where('companyID', $companyID)->get()->row('formulaString');
        } else if ($decodeType == 'is_noPaySetup') {
            $formula = $this->db->select('formulaString')->from('srp_erp_nopayformula')
                ->where('id', $payGroupID)->where('companyID', $companyID)->get()->row('formulaString');
        }else if ($decodeType == 'is_sickLeaveSetup') {
            $postData = explode('|', $payGroupID);
            $leaveID = $postData[0];
            $isNonPayroll = $postData[1];
            $formula = $this->db->select('formulaString')->from('srp_erp_sickleavesetup')
                ->where('leaveTypeID', $leaveID)->where('isNonPayroll', $isNonPayroll)->where('companyID', $companyID)->get()->row('formulaString');
        } else {
            $formula = $this->db->select('formulaString')->from('srp_erp_paygroupformula')
                ->where('payGroupID', $payGroupID)->where('companyID', $companyID)->get()->row('formulaString');
        }


        $formulaDecodeData = ['decodedList' => ''];

        if (!empty($formula) && $formula != null) {
            $formulaDecodeData['decodedList'] = formulaDecode($formula);
        }

        echo json_encode($formulaDecodeData);
    }

    /*Pay group*/

    function fetch_paygroupmaster()
    {
        $this->datatables->select('t1.description AS description,t1.payGroupID AS payGroupID,t1.isGroupTotal AS isGroupTotal', false)
            ->from('srp_erp_paygroupmaster t1')
            ->where('t1.companyID', current_companyID())
            ->where('t1.isGroupTotal', 1)
            ->add_column('actions', '$1', 'action_payGroup(payGroupID,description,isGroupTotal)');
        echo $this->datatables->generate();
    }

    function savePayGroup()
    {
        $this->form_validation->set_rules('description[]', 'Description', 'required');
        $isGroupTotal = $this->input->post('isGroupTotal');

        if (!is_null($isGroupTotal)) {
//            $this->form_validation->set_rules('expenseGlAutoID[]', 'Expense Gl Code', 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->savePayGroup());
        }
    }

    function updatePayGroup()
    {
        $this->form_validation->set_rules('pgDes', 'Description', 'required');
        $this->form_validation->set_rules('hidden-id', 'ID', 'required');

        $pgIsGroupTotal = $this->input->post('pgIsGroupTotal');

        if (is_null($pgIsGroupTotal)) {
//            $this->form_validation->set_rules('pgexpenseGlAutoID', 'Expense GL Code', 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->updatePayGroup());
        }
    }

    function getSalaryCategoriesByGroupId()
    {
        $companyID = current_companyID();
        $payGroupId = $this->input->post('payGroupId');

        $payGroupDetails = $this->db->query("SELECT
srp_erp_paygroupdetails . salaryCategoryID,
srp_erp_paygroupdetails . salaryCategoryID,
srp_erp_paygroupmaster . description,
srp_erp_paygroupmaster . socialInsuranceID,
srp_erp_pay_salarycategories . salaryDescription
FROM
srp_erp_paygroupdetails
INNER JOIN srp_erp_paygroupmaster ON srp_erp_paygroupdetails . groupID = srp_erp_paygroupmaster . payGroupID
INNER JOIN srp_erp_pay_salarycategories ON srp_erp_paygroupdetails . salaryCategoryID = srp_erp_pay_salarycategories . salaryCategoryID
WHERE srp_erp_paygroupdetails . companyID = '{$companyID}' AND payGroupID = '{$payGroupId}' ")->result_array();

        $retrun = '';
        foreach ($payGroupDetails as $payGroupDetail) {
            $retrun .= '<a class="btn btn - sm btn -default"
                                                   onclick="appendFormula(\'' . $payGroupDetail['salaryDescription'] . '\',\'' . $payGroupDetail['salaryCategoryID'] . '\',\'' . $payGroupDetail['salaryCategoryID'] . '\',1)"
                                                   href="#"><strong>' . $payGroupDetail['salaryDescription'] . '</strong></a>';
        }

//        echo $this->db->last_query();
        echo $retrun;
    }

    function Load_GLCode_for_fixed_allowance()
    {
        $companyID = current_companyID();
        $masterID = $this->input->post('masterID');

        $slabDetail = $this->db->query("SELECT salaryCategoryID,companyID,GLCode FROM `srp_erp_pay_salarycategories` WHERE `companyID` = '{$companyID}' AND salaryCategoryID = '{$masterID}'")->row_array();
        echo json_encode($slabDetail);
    }


    function deletePayGroup()
    {
        $this->form_validation->set_rules('hidden-id', 'Page Group ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->deletePayGroup());
        }
    }

    public function leaveaccrualMaster()
    {
        $this->datatables->select('leaveaccrualMasterID,leaveaccrualMasterCode,srp_erp_leaveaccrualmaster.description,concat(year,\' - \',MONTHNAME(STR_TO_DATE(month, \'%m\'))) as month,srp_erp_leavegroup.Description as leaveGroup, confirmedYN', false)
            ->from('srp_erp_leaveaccrualmaster')
            ->join('srp_erp_leavegroup', 'srp_erp_leaveaccrualmaster.leaveGroupID = srp_erp_leavegroup.leaveGroupID', 'left')
            ->add_column('edit', '$1', 'accrualAction(leaveaccrualMasterID, confirmedYN)')
            ->where('manualYN', 0)
            ->where('srp_erp_leaveaccrualmaster.policyMasterID', 3)
            ->where('srp_erp_leaveaccrualmaster.companyID', current_companyID());
        echo $this->datatables->generate();
    }

    public function LeaveAccrualdetails()
    {
        $masterID = $this->input->post('masterID');

        $header = $this->db->query("SELECT leaveType,srp_erp_leavetype.description FROM `srp_erp_leaveaccrualdetail` 
                    LEFT JOIN `srp_erp_leavetype` ON srp_erp_leavetype.leaveTypeID = srp_erp_leaveaccrualdetail.leaveType 
                    WHERE leaveaccrualMasterID = {$masterID} group by leaveType order by description asc")->result_array();
        $data['details'] = false;
        $select = '';
        if (!empty($header)) {
            foreach ($header as $val) {
                $string = str_replace(' ', '', $val['description']);
                $select .= "sum(if(leaveType='{$val['leaveType']}',daysEntitled,0)) as '{$string}',";
            }
            $qry = "SELECT $select CONCAT(ECode,' - ',Ename2) as Ename2,empID,srp_erp_leaveaccrualdetail.description,daysEntitled,srp_erp_leavetype.description as leavetype,srp_erp_leavetype.leaveTypeID FROM `srp_erp_leaveaccrualdetail` LEFT JOIN `srp_employeesdetails` on EidNo=empID LEFT JOIN `srp_erp_leavetype`  on srp_erp_leavetype.leaveTypeID=srp_erp_leaveaccrualdetail.leaveType WHERE leaveaccrualMasterID = {$masterID} group by empID order by Ename2 ";
            $data['details'] = $this->db->query($qry)->result_array();
            //echo $this->db->last_query();


        }

        $data['confirmedYN'] = $this->db->query("select confirmedYN from srp_erp_leaveaccrualmaster WHERE leaveaccrualMasterID=$masterID ")->row_array();

        $data['header'] = $header;
        $html = $this->load->view('system/hrm/leave_accrual_detail_table', $data, true);
        echo $html;
    }

    public function getAccrualHeader()
    {
        $masterID = $this->input->post('masterID');
        $data = $this->db->query("select * from srp_erp_leaveaccrualmaster where leaveaccrualMasterID={$masterID} ")->row_array();
        echo json_encode($data);
    }

    public function leaveCalenderEvent()
    {
        $start = $this->input->get('start');
        $end = $this->input->get('end');
        $event_array = array();
        $event_array2 = array();
        $companyID = current_companyID();


        $sql2 = "SELECT *, DATE_ADD(endDate, INTERVAL 1 DAY) endDate2 FROM srp_erp_calenderevent WHERE startDate >='{$start}' AND endDate <='{$end}' AND companyID={$companyID} ";
        $sql = "SELECT fulldate FROM srp_erp_calender WHERE weekend_flag=1 AND fulldate BETWEEN '{$start}' AND '{$end}' AND companyID={$companyID}";
        $result = $this->db->query($sql)->result_array();
        $result2 = $this->db->query($sql2)->result_array();

        foreach ($result as $record) {
            $event_array[] = array(
                'id' => 'Leave',
                'start' => $record['fulldate'],
                'end' => $record['fulldate'],
                'rendering' => 'background',
                'backgroundColor' => '#009933'

            );
        }
        foreach ($result2 as $record2) {
            if ($record2['type'] == 1) {
                $datetime = new DateTime($record2['startDate'], new DateTimeZone('America/Chicago'));
                $datetime_string = $datetime->format('c');
                $enddatetime = new DateTime($record2['endDate'], new DateTimeZone('America/Chicago'));
                $enddatetime_string = $enddatetime->format('c');
                $record2['startDate'] = $datetime_string;
                $date = strtotime("-1 day", strtotime($record2['endDate']));
                $record2['endDate'] = $enddatetime_string;
                $event_array2[] = array(
                    'id' => $record2['eventID'],
                    'title' => $record2['title'],
                    'start' => $record2['startDate'],
                    'end' => $record2['endDate2'],
                    'color' => '#AB47BC'
                );
            } else {


                $start = strtotime($record2['startDate']);
                $startDate = date('Y-m-d', $start);
                $end = strtotime($record2['endDate2']);
                $endDate = date('Y-m-d', $end);

                $event_array2[] = array(
                    'id' => $record2['eventID'],
                    'title' => $record2['title'],
                    'start' => $startDate,
                    'end' => $endDate,
                    'color' => '#ff8a80',
                    'allDay' => true


                );
            }

        }

        $arr = array_merge($event_array2, $event_array);

        /* print_r($arr);*/

        echo json_encode($arr);
    }

    public function leaveCalender_insert()
    {

        $postedWithAccrual = $this->input->post('postedWithAccrual');
        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');
        $companyID = current_companyID();
        $type = $this->input->post('type');  // type =1 event , type =2 holiday


        /***************************************************************************************************************
         If $postedWithAccrual != 'Y' than
           - check whether there is/are leave date falling on selected date range
           - if there are leave can founded in this date range than return data to leave accrual confirmation

         If $postedWithAccrual == 'Y' it means this function came with leave accruals confirmation
        ***************************************************************************************************************/


        if($type == 2) {

            if ($postedWithAccrual != 'Y') {
                $startDate_app = date('Y-m-d', strtotime($startDate));
                $endDate_app = date('Y-m-d', strtotime($endDate . ' -1 day'));

                /**** Get if there are leave application in this date with isCalenderDays = 0 ***/
                $holiday = $this->db->query("SELECT eventID, title, DATE(startDate) AS sDate, DATE(endDate) AS eDate
                                             FROM srp_erp_calenderevent WHERE companyID={$companyID} AND `type` = 2
                                             AND (
                                                  ( '{$startDate_app}' BETWEEN DATE(startDate) AND DATE(endDate) )
                                                  OR ( '{$endDate_app}' BETWEEN DATE(startDate) AND DATE(endDate) )
                                                  OR ( DATE(startDate) BETWEEN '{$startDate_app}' AND '{$endDate_app}' )
                                                  OR ( DATE(endDate) BETWEEN '{$startDate_app}' AND '{$endDate_app}' )
                                              )")->result_array();

                if(!empty($holiday)){
                    $msg = '';
                    foreach($holiday as $rowH){
                        $msg .= $rowH['title'].' => '.$rowH['sDate'].' &nbsp;-&nbsp; '.$rowH['eDate'].' <br/>';
                    }
                    die( json_encode(['e', 'Following holidays is/are already declared in this date range<br/>'.$msg]));
                }


                /**** Get if there are leave application in this date with isCalenderDays = 0 ***/
                $leaveApp = $this->db->query("SELECT leaveMasterID, documentCode, lType.description, empTB.ECode, Ename2, startDate, endDate
                                      FROM srp_erp_leavemaster AS lMaster
                                      JOIN srp_erp_leavetype AS lType ON lType.leaveTypeID=lMaster.leaveTypeID AND lType.companyID={$companyID}
                                      JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=lMaster.empID AND Erp_companyID={$companyID}
                                      WHERE lMaster.companyID={$companyID} AND applicationType = 1 AND lMaster.confirmedYN=1 AND isCalenderDays=0
                                      AND (
                                          ( ( '{$startDate_app}' BETWEEN startDate AND endDate ) OR ( '{$endDate_app}' BETWEEN startDate AND endDate ) )
                                            OR
                                          ( ( startDate BETWEEN '{$startDate_app}' AND '{$endDate_app}' ) OR ( endDate BETWEEN '{$startDate_app}' AND '{$endDate_app}' ) )
                                      )")->result_array();


                if (!empty($leaveApp)) {
                    $endDate_period = date('Y-m-d', strtotime($endDate));
                    $period1 = new DatePeriod(
                        new DateTime($startDate_app),
                        new DateInterval('P1D'),
                        new DateTime($endDate_period)
                    );


                    /**** Get already declared as holiday/weekend ****/
                    $isLeaveDeclared = $this->db->query("SELECT fulldate FROM srp_erp_calender WHERE companyID={$companyID}
                                                     AND fulldate BETWEEN '{$startDate_app}' AND '{$endDate_app}' AND
                                                     (holiday_flag=1 OR weekend_flag=1)")->result_array();

                    $leaveDeclaredDate = [];
                    if (!empty($isLeaveDeclared)) {
                        $leaveDeclaredDate = array_column($isLeaveDeclared, 'fulldate');
                    }

                    $period = [];
                    foreach ($period1 as $date) {
                        $periodDate = $date->format("Y-m-d");
                        if (!empty($leaveDeclaredDate)) {
                            /*** Only add undeclared holiday/weekend dates to period array***/
                            if ((!in_array($periodDate, $leaveDeclaredDate))) {
                                $period[] = $periodDate;
                            }
                        } else {
                            $period[] = $periodDate;
                        }
                    }


                    $table = '<strong>Holiday Date Range</strong> &nbsp;: ' . $startDate_app . ' &nbsp;-&nbsp; ' . $endDate_app;
                    $table .= '<table class="' . table_class() . '" id="accrual-data-table">
                            <thead>
                            <tr style="white-space: nowrap">
                                <th>#</th>
                                <th>Employee</th>
                                <th>Document Code </th>
                                <th>Leave Date</th>
                                <th>Leave Type</th>
                                <th>Accrual Date</th>
                                <th><input type="checkbox" id="checkAllLeave" onclick="checkAllLeave(this)"/></th>
                            </tr>
                            </thead>
                            <tbody>';
                    foreach ($leaveApp as $key => $app) {

                        $row_str = date('Y-m-d', strtotime($app['startDate']));
                        $row_end = date('Y-m-d', strtotime($app['endDate']));

                        $period_int = new DatePeriod(
                            new DateTime($row_str),
                            new DateInterval('P1D'),
                            new DateTime(date('Y-m-d', strtotime($row_end . ' +1 day')))
                        );


                        foreach ($period_int as $date) {
                            $periodDate = $date->format("Y-m-d");
                            /*** dates that are fall on applied leaves with in selected holiday range ***/
                            if((in_array($periodDate, $period))) {
                                $checkVal = $app['leaveMasterID'] . '|' . $periodDate;
                                $table .= '<tr>';
                                $table .= '<td>' . ($key + 1) . '</td>';
                                $table .= '<td>' . $app['ECode'] . '-' . $app['Ename2'] . '</td>';
                                $table .= '<td>' . $app['documentCode'] . '</td>';
                                $table .= '<td>' . $row_str . ' &nbsp;-&nbsp; ' . $row_end . '</td>';
                                $table .= '<td>' . $app['description'] . '</td>';
                                $table .= '<td>' . $periodDate . '</td>';
                                $table .= '<td align="center">
                                             <input type="checkbox" class="leaveAccruals" value="' . $checkVal . '" onclick="unCheckLeave()"/>
                                           </td>';
                                $table .= '</tr>';
                            }
                        }
                    }

                    $table .= '</tbody></table>';

                    /**** Return data to accrual confirmation ****/
                    die(json_encode(['c', $table]));
                }

            }
            else {

                $accID_list_post = $this->input->post('accID_list');

                if (!empty($accID_list_post)) {

                    $accID_list = '';
                    $accID_arr = [];
                    foreach ($accID_list_post as $keyList => $list) {
                        $list_arr = explode('|', $list);
                        $listID = $list_arr[0];
                        $listDate = $list_arr[1];

                        $sep = ($keyList > 0) ? ',' : '';
                        $accID_list .= $sep . $listID;

                        /**** Implementing a array based on the leave master id ($listID), To collect their relative accrual dates  ****/
                        $accID_arr[$listID][] = $listDate;

                    }


                    /**** Get the data of selected leave application ***/
                    $leaveApp = $this->db->query("SELECT leaveMasterID, documentCode, empID, lMaster.leaveTypeID, lType.description, empTB.leaveGroupID,
                                              policyMasterID, approvedYN, startDate, ishalfDay
                                              FROM srp_erp_leavemaster AS lMaster
                                              JOIN srp_erp_leavetype AS lType ON lType.leaveTypeID=lMaster.leaveTypeID AND lType.companyID={$companyID}
                                              JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=lMaster.empID AND Erp_companyID={$companyID}
                                              WHERE lMaster.companyID={$companyID} AND applicationType = 1 AND lMaster.leaveMasterID IN ({$accID_list})
                                              AND lMaster.confirmedYN=1 AND isCalenderDays=0")->result_array();

                    if (!empty($leaveApp)) {

                        $this->db->trans_start();
                        $calendarReturn = $this->leaveAccrual_onCalenderUpdate();
                        $calendarHolidayID = $calendarReturn[2];


                        foreach ($leaveApp as $app) {

                            $accDet = [];
                            $leaveMasterID = $app['leaveMasterID'];
                            $daysEntitle = ($app['ishalfDay'] == 1)? '0.5': count($accID_arr[$leaveMasterID]);
                            $period = $app['startDate'];
                            $approvedYN = $app['approvedYN'];
                            $d = explode('-', $period);
                            $description = 'Leave Accrual for holidays ';
                            $comment = $description . ' - ' . $app['documentCode'];
                            $leaveGroupID = $app['leaveGroupID'];
                            $policyMasterID = $app['policyMasterID'];
                            $this->load->library('sequence');
                            $code = $this->sequence->sequence_generator('LAM');


                            $accMaster = array(
                                'companyID' => current_companyID(),
                                'leaveaccrualMasterCode' => $code,
                                'documentID' => 'LAM',
                                'leaveMasterID' => $leaveMasterID,
                                'calendarHolidayID' => $calendarHolidayID,
                                'description' => $comment,
                                'year' => $d[0],
                                'month' => $d[1],
                                'leaveGroupID' => $leaveGroupID,
                                'policyMasterID' => $policyMasterID,
                                'createdUserGroup' => current_user_group(),
                                'createDate' => current_date(),
                                'createdpc' => current_pc(),
                            );

                            if ($approvedYN == 1) {
                                $accMaster['confirmedYN'] = 1;
                                $accMaster['confirmedby'] = current_userID();
                                $accMaster['confirmedDate'] = current_date();
                            }


                            $this->db->insert('srp_erp_leaveaccrualmaster', $accMaster);


                            $accDet['leaveaccrualMasterID'] = $this->db->insert_id();
                            $accDet['empID'] = $app['empID'];
                            $accDet['comment'] = '';
                            $accDet['leaveGroupID'] = $leaveGroupID;
                            $accDet['leaveType'] = $app['leaveTypeID'];
                            $accDet['daysEntitled'] = $daysEntitle;
                            $accDet['comment'] = $comment;
                            $accDet['description'] = $description;
                            $accDet['calendarHolidayID'] = $calendarHolidayID;
                            $accDet['leaveMasterID'] = $leaveMasterID;
                            $accDet['createDate'] = current_date();
                            $accDet['createdUserGroup'] = current_user_group();
                            $accDet['createdPCid'] = current_pc();

                            $this->db->insert('srp_erp_leaveaccrualdetail', $accDet);

                        }

                        $this->db->trans_complete();

                        if ($this->db->trans_status() == true) {
                            $this->db->trans_commit();
                            die(json_encode(['s', 'Calender successfully updated']));

                        } else {
                            $this->db->trans_rollback();
                            die(json_encode(['e', 'Error in process.']));
                        }
                    }
                }

            }
        }
        $this->db->trans_start();
        $returnData = $this->leaveAccrual_onCalenderUpdate();
        $this->db->trans_complete();

        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            echo json_encode($returnData);

        }else{
            echo json_encode(['e', 'Error in process.']);
            $this->db->trans_rollback();
        }
    }

    function leaveAccrual_onCalenderUpdate(){
        $title = $this->input->post('title');
        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');
        $companyID = current_companyID();
        $type = $this->input->post('type');  // type =1 event , type =2 holiday
        $endDate = date('Y-m-d H:i:s', strtotime($endDate.' - 1 day'));

        if($type == 2){
            $startDate = date('Y-m-d', strtotime($startDate));
            $endDate = date('Y-m-d', strtotime($endDate));
        }

        $calender_data = [
            'title' => $title,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'url' => '',
            'allDay' => '',
            'companyID' => $companyID,
            'type' => $type
        ];

        $this->db->insert('srp_erp_calenderevent', $calender_data);
        $calendarHolidayID = $this->db->insert_id();


        if ($type != 1) {
            $col = 'holiday_flag';

            $this->db->where("fulldate BETWEEN DATE('$startDate') AND DATE('$endDate') ");
            $this->db->where("companyID", $companyID);
            $this->db->update('srp_erp_calender', array($col => 1));
        }

        return ['s', 'Calender successfully updated', $calendarHolidayID];
    }

    public function delete_event()
    {
        $id = $this->input->post('id');
        $companyID = current_companyID();

        $this->db->trans_start();
        $result = $this->db->query("SELECT startDate,endDate,type FROM srp_erp_calenderevent WHERE eventID={$id} AND companyID={$companyID}")->row_array();

        if ($result['type'] == 2) {
            $r_startDate = $result['startDate'];
            $r_endDate = $result['endDate'];

            /**** Get if there are leave application in this date with isCalenderDays = 0 ***/
            $leaveApp = $this->db->query("SELECT leaveMasterID, documentCode, lType.description, empTB.ECode, Ename2, startDate, endDate
                                      FROM srp_erp_leavemaster AS lMaster
                                      JOIN srp_erp_leavetype AS lType ON lType.leaveTypeID=lMaster.leaveTypeID AND lType.companyID={$companyID}
                                      JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=lMaster.empID AND Erp_companyID={$companyID}
                                      WHERE lMaster.companyID={$companyID} AND applicationType = 1 AND lMaster.confirmedYN=1 AND isCalenderDays=0
                                      AND (
                                          ( ( '{$r_startDate}' BETWEEN startDate AND endDate ) OR ( '{$r_endDate}' BETWEEN startDate AND endDate ) )
                                            OR
                                          ( ( startDate BETWEEN '{$r_startDate}' AND '{$r_endDate}' ) OR ( endDate BETWEEN '{$r_startDate}' AND '{$r_endDate}' ) )
                                      )")->result_array();


            if(!empty($leaveApp)){
                $leaveID_arr = array_column($leaveApp, 'leaveMasterID');
                $leaveMasterID_list = implode(',' ,$leaveID_arr);


                $accrualData = $this->db->query("SELECT leaveMasterID, calendarHolidayID, confirmedYN FROM srp_erp_leaveaccrualmaster
                                                 WHERE companyID={$companyID} AND leaveMasterID IN($leaveMasterID_list)")->result_array();

                $accrualLeaveID_arr = array_column($accrualData, 'leaveMasterID');

                $holidayDateRange = new DatePeriod(
                    new DateTime($r_startDate),
                    new DateInterval('P1D'),
                    new DateTime(date('Y-m-d', strtotime($r_endDate . ' +1 day')))
                );


                $holidayPeriod = [];
                foreach ($holidayDateRange as $date) {
                    $periodDate = $date->format("Y-m-d");
                    $holidayPeriod[] = $periodDate;
                }

                $isPendingAccrual = false;
                $table = '<form id="holidayAccrual_form" > <input type="hidden" name="holidayID" value="'.$id.'" />';
                $table .= '<strong>Holiday Date Range</strong> &nbsp;: ' . date('Y-m-d', strtotime($r_startDate));
                $table .= '&nbsp;<strong>-</strong>&nbsp; ' . date('Y-m-d', strtotime($r_endDate));
                $table .= '<table class="' . table_class() . '" id="accrual-data-table">
                            <thead>
                            <tr style="white-space: nowrap">
                                <th>#</th>
                                <th>Employee</th>
                                <th>Document Code </th>
                                <th>Leave Date</th>
                                <th>Leave Type</th>
                                <th>Accrual Date</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>';
                //<input type="checkbox" id="checkAllLeave" onclick="checkAllLeave(this)"/>

                $n = 1;
                foreach($leaveApp as $key=>$leaveData){
                    $lID = $leaveData['leaveMasterID'];
                    if(!in_array($lID, $accrualLeaveID_arr)){

                        $leave_startDate = date('Y-m-d', strtotime($leaveData['startDate']));
                        $leave_endDate = date('Y-m-d', strtotime($leaveData['endDate']));
                        $leavePeriod = new DatePeriod(
                            new DateTime($leave_startDate),
                            new DateInterval('P1D'),
                            new DateTime(date('Y-m-d', strtotime($leave_endDate . ' +1 day')))
                        );


                        foreach ($leavePeriod as $date) {
                            $leaveDate = $date->format("Y-m-d");

                            if(in_array($leaveDate, $holidayPeriod)){
                                $isPendingAccrual = true;
                                $checkVal = $leaveData['leaveMasterID'] . '|' . $leaveDate;
                                $table .= '<tr>';
                                $table .= '<td>' . $n . '</td>';
                                $table .= '<td>' . $leaveData['ECode'] . '-' . $leaveData['Ename2'] . '</td>';
                                $table .= '<td>' . $leaveData['documentCode'] . '</td>';
                                $table .= '<td>' . $leave_startDate . ' &nbsp;-&nbsp; ' . $leave_endDate . '</td>';
                                $table .= '<td>' . $leaveData['description'] . '</td>';
                                $table .= '<td>' . $leaveDate . '</td>';
                                $table .= '<td align="center">
                                             <input type="checkbox" name="accID_list[]" class="leaveAccruals" value="' . $checkVal . '" onclick="unCheckLeave()"/>
                                           </td>';
                                $table .= '</tr>';

                                $n++;
                            }
                        }
                    }

                }

                $table .= '</tbody></table></form>';

                /**** Return data to accrual confirmation ****/
                if($isPendingAccrual == true){
                    die(json_encode(['c', $table]));
                }
            }


            $valid = $this->db->query("SELECT startDate,endDate,type FROM  srp_erp_calenderevent
                                       WHERE (DATE(startDate) BETWEEN DATE('{$r_startDate}') AND DATE('{$r_endDate}') OR DATE(endDate)
                                       BETWEEN DATE('{$r_startDate}') AND DATE('{$r_endDate}'))
                                       AND companyID={$companyID} AND type=2 AND eventID !={$id}")->result_array();


            if (empty($valid)) {
                /* $endDatee = strtotime($r_endDate);
                 $endDatee = date('Y-m-d', strtotime('-1 day', $endDatee));*/
                $this->db->where("companyID", $companyID);
                $this->db->where("fulldate BETWEEN date('{$r_startDate}') AND date('{$r_endDate}') ");
                $this->db->update('srp_erp_calender', array('holiday_flag' => 0));
            } else {

                foreach ($valid as $dateValue) {
                    $dates = $this->getDatesFromRange($dateValue['startDate'], $dateValue['endDate']);
                    foreach ($dates as $val) {
                        $detail[] = $val;
                    }
                }
                array_unique($detail);

                if (!empty($detail)) {
                    $dateFilter = "'" . implode("', '", $detail) . "'";
                    $this->db->where("companyID", $companyID);
                    $this->db->where("fulldate BETWEEN date('{$r_startDate}') AND date('{$r_endDate}') ");
                    $this->db->where("fulldate NOT IN ($dateFilter) ");
                    $this->db->update('srp_erp_calender', array('holiday_flag' => 0));
                }
            }

            /*** Delete leave accruals of this calender event/leave ***/
            $this->db->delete('srp_erp_leaveaccrualmaster', ['calendarHolidayID'=>$id, 'companyID'=>$companyID]);
            $this->db->delete('srp_erp_leaveaccrualdetail', ['calendarHolidayID'=>$id]);
        }


        $this->db->delete('srp_erp_calenderevent', array('eventID' => $id));

        $this->db->trans_complete();

        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            echo json_encode(['s', 'Deleted successfully.']);
        }else{
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in process.']);
        }
    }

    function delete_eventWithAccrual(){
        $id = $this->input->post('holidayID');
        $accID_list_post = $this->input->post('accID_list');
        $companyID = current_companyID();

        $this->db->trans_start();
        $result = $this->db->query("SELECT startDate,endDate,type FROM srp_erp_calenderevent WHERE eventID={$id} AND companyID={$companyID}")->row_array();

        $r_startDate = $result['startDate'];
        $r_endDate = $result['endDate'];

        /*** Delete leave accruals of this calender event/leave that are added on this holiday create***/
        $this->db->delete('srp_erp_leaveaccrualmaster', ['calendarHolidayID'=>$id, 'companyID'=>$companyID]);
        $this->db->delete('srp_erp_leaveaccrualdetail', ['calendarHolidayID'=>$id]);


        if (!empty($accID_list_post)) {

            $accID_list = '';
            $accID_arr = [];
            foreach ($accID_list_post as $keyList => $list) {
                $list_arr = explode('|', $list);
                $listID = $list_arr[0];
                $listDate = $list_arr[1];

                $sep = ($keyList > 0) ? ',' : '';
                $accID_list .= $sep . $listID;

                /**** Implementing a array based on the leave master id ($listID), To collect their relative accrual dates  ****/
                $accID_arr[$listID][] = $listDate;

            }
            //echo '<pre>'; print_r($accID_arr); echo '</pre>';

            /**** Get the data of selected leave application ***/
            $leaveApp = $this->db->query("SELECT leaveMasterID, documentCode, empID, lMaster.leaveTypeID, lType.description, empTB.leaveGroupID,
                                          policyMasterID, approvedYN, startDate, ishalfDay
                                          FROM srp_erp_leavemaster AS lMaster
                                          JOIN srp_erp_leavetype AS lType ON lType.leaveTypeID=lMaster.leaveTypeID AND lType.companyID={$companyID}
                                          JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=lMaster.empID AND Erp_companyID={$companyID}
                                          WHERE lMaster.companyID={$companyID} AND applicationType = 1 AND lMaster.leaveMasterID IN ({$accID_list})
                                          AND lMaster.confirmedYN=1")->result_array();
            //echo '<pre>'; print_r($leaveApp); echo '</pre>';        die();
            if (!empty($leaveApp)) {

                $this->db->trans_start();
                $calendarHolidayID = null;

                foreach ($leaveApp as $app) {

                    $accDet = [];
                    $leaveMasterID = $app['leaveMasterID'];
                    $daysEntitle = ($app['ishalfDay'] == 1) ? '0.5' : count($accID_arr[$leaveMasterID]);
                    $period = $app['startDate'];
                    $approvedYN = $app['approvedYN'];
                    $d = explode('-', $period);
                    $description = 'Leave Accrual for holiday delete ';
                    $comment = $description . ' - ' . $app['documentCode'];
                    $leaveGroupID = $app['leaveGroupID'];
                    $policyMasterID = $app['policyMasterID'];
                    $this->load->library('sequence');
                    $code = $this->sequence->sequence_generator('LAM');


                    $accMaster = [
                        'companyID' => current_companyID(),
                        'leaveaccrualMasterCode' => $code,
                        'documentID' => 'LAM',
                        'leaveMasterID' => $leaveMasterID,
                        'calendarHolidayID' => $calendarHolidayID,
                        'description' => $comment,
                        'year' => $d[0],
                        'month' => $d[1],
                        'leaveGroupID' => $leaveGroupID,
                        'policyMasterID' => $policyMasterID,
                        'createdUserGroup' => current_user_group(),
                        'createDate' => current_date(),
                        'createdpc' => current_pc(),
                    ];

                    if ($approvedYN == 1) {
                        $accMaster['confirmedYN'] = 1;
                        $accMaster['confirmedby'] = current_userID();
                        $accMaster['confirmedDate'] = current_date();
                    }


                    $this->db->insert('srp_erp_leaveaccrualmaster', $accMaster);


                    $accDet['leaveaccrualMasterID'] = $this->db->insert_id();
                    $accDet['empID'] = $app['empID'];
                    $accDet['comment'] = '';
                    $accDet['leaveGroupID'] = $leaveGroupID;
                    $accDet['leaveType'] = $app['leaveTypeID'];
                    $accDet['daysEntitled'] = ($daysEntitle * -1);
                    $accDet['comment'] = $comment;
                    $accDet['description'] = $description;
                    $accDet['calendarHolidayID'] = $calendarHolidayID;
                    $accDet['leaveMasterID'] = $leaveMasterID;
                    $accDet['createDate'] = current_date();
                    $accDet['createdUserGroup'] = current_user_group();
                    $accDet['createdPCid'] = current_pc();

                    $this->db->insert('srp_erp_leaveaccrualdetail', $accDet);

                }

            }
        }



        $valid = $this->db->query("SELECT startDate,endDate,type FROM  srp_erp_calenderevent
                                   WHERE (DATE(startDate) BETWEEN DATE('{$r_startDate}') AND DATE('{$r_endDate}') OR DATE(endDate)
                                   BETWEEN DATE('{$r_startDate}') AND DATE('{$r_endDate}'))
                                   AND companyID={$companyID} AND type=2 AND eventID !={$id}")->result_array();


        if (empty($valid)) {
            $this->db->where("companyID", $companyID);
            $this->db->where("fulldate BETWEEN date('{$r_startDate}') AND date('{$r_endDate}') ");
            $this->db->update('srp_erp_calender', array('holiday_flag' => 0));
        } else {

            foreach ($valid as $dateValue) {
                $dates = $this->getDatesFromRange($dateValue['startDate'], $dateValue['endDate']);
                foreach ($dates as $val) {
                    $detail[] = $val;
                }
            }
            array_unique($detail);

            if (!empty($detail)) {
                $dateFilter = "'" . implode("', '", $detail) . "'";
                $this->db->where("companyID", $companyID);
                $this->db->where("fulldate BETWEEN date('{$r_startDate}') AND date('{$r_endDate}') ");
                $this->db->where("fulldate NOT IN ($dateFilter) ");
                $this->db->update('srp_erp_calender', array('holiday_flag' => 0));
            }
        }



        $this->db->delete('srp_erp_calenderevent', array('eventID' => $id));

        $this->db->trans_complete();

        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            echo json_encode(['s', 'Deleted successfully.']);
        }else{
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in process.']);
        }
    }

    public function searchLeaveemployee()
    {
        $keyword = $this->input->get('keyword');
        $emp = $this->Employee_model->searchleaveEmployee($keyword);


        if (!empty($emp)) {
            echo json_encode($emp);
        } else {
            $noData[0] = array(
                'DesDescription' => '',
                'EIdNo' => '',
                'Ename1' => '',
                'Ename2' => '',
                'Ename3' => '',
                'ECode' => '',
                'Match' => 'No records',
            );
            echo json_encode($noData);
        }
    }


    function loadLeaveDropDown()
    {
        $empID = $this->input->post('empID');

        $leaveTypes = $this->db->query("SELECT srp_erp_leavetype.* FROM `srp_employeesdetails` LEFT JOIN `srp_erp_leavegroupdetails` ON `srp_employeesdetails`.`leaveGroupID` = `srp_erp_leavegroupdetails`.`leaveGroupID` LEFT JOIN `srp_erp_leavetype` on srp_erp_leavegroupdetails.leaveTypeID=srp_erp_leavetype.leaveTypeID WHERE EidNo = '{$empID}'")->result_array();

        $html = "<select name='leaveType' class='form-control frm_input' id='leaveType'>";
        $html .= "<option value=''>Select a Type</option>";
        if ($leaveTypes) {
            foreach ($leaveTypes as $leave) {
                $html .= '<option value="' . $leave['leaveTypeID'] . '" data-value="' . $leave['isPaidLeave'] . '">' . $leave['description'] . '</option>';
            }
        }
        $html .= '</select>';

        echo $html;

    }

    function save_leave_adjustment()
    {
        $this->Employee_model->save_leaveAdjustment();
    }

    public function leaveAdjustmentMaster()
    {
        $this->datatables->select('leaveaccrualMasterID,leaveaccrualMasterCode,srp_erp_leaveaccrualmaster.description as description  ,concat(year,\' - \',MONTHNAME(STR_TO_DATE(month, \'%m\'))) as month,srp_erp_leavegroup.Description as leaveGroup,confirmedYN', false)
            ->from('srp_erp_leaveaccrualmaster')
            ->join('srp_erp_leavegroup', 'srp_erp_leaveaccrualmaster.leaveGroupID = srp_erp_leavegroup.leaveGroupID', 'left')
            ->add_column('edit', '$1', 'leaveAdjustmentAction(leaveaccrualMasterID,confirmedYN)')
            ->add_column('confirmedYN', '$1', 'confirm(confirmedYN)')
            ->where('manualYN', 1)
            ->where('srp_erp_leaveaccrualmaster.companyID', current_companyID());
        echo $this->datatables->generate();
    }

    function leaveAdjustmentDetail()
    {
        $masterID = $this->input->post('masterID');
        $companyID = current_companyID();

        $header = $this->db->query("SELECT accMaster.leaveGroupID,leaveType, lType.description, leaveTypeData.policyMasterID, isCarryForward                           
                            FROM srp_erp_leaveaccrualmaster AS accMaster
                            LEFT JOIN srp_erp_leaveaccrualdetail AS accDet ON accMaster.leaveaccrualMasterID = accDet.leaveaccrualMasterID 
                            LEFT JOIN srp_erp_leavetype AS lType ON lType.leaveTypeID = accDet.leaveType 
                            LEFT JOIN(
                                SELECT gMaster.leaveGroupID, leaveTypeID, policyMasterID, isCarryForward 
                                FROM srp_erp_leavegroup AS gMaster
                                JOIN srp_erp_leavegroupdetails AS gDet ON gMaster.leaveGroupID=gDet.leaveGroupID
                                WHERE companyID = {$companyID}
                            ) AS leaveTypeData ON leaveTypeData.leaveGroupID=accMaster.leaveGroupID 
                            AND leaveTypeData.leaveTypeID = accDet.leaveType
                            WHERE accMaster.leaveaccrualMasterID = {$masterID}  
                            GROUP BY leaveType ORDER BY lType.description ASC")->result_array();

        $data['details'] = FALSE;
        $select = '';
        $select2 = '';

        $detailCount = $this->db->get_where('srp_erp_leaveaccrualdetail', ['leaveaccrualMasterID'=>$masterID])->num_rows();

        if ($detailCount > 0) {
            $currentYearFirstDate = date('Y-01-01');
            $currentYearLastDate = date('Y-12-31');

            foreach ($header as $val) {
                $isCarryForward = $val['isCarryForward'];
                $policyMasterID = $val['policyMasterID'];
                $leaveGroupID = $val['leaveGroupID'];
                $string = str_replace(' ', '', $val['description']);
                $lType = $val['leaveType'];

                if ($policyMasterID == 2) {
                    $select .= "sum(if(leaveType='{$lType}',hoursEntitled,0)) as '{$string}',";
                    $select2 .= "sum(if(leaveType='{$lType}',hoursEntitled,0)) - IFNULL( ( SELECT SUM(if(leaveTypeID='{$lType}',hours,0)) FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.empID = empTB.EIdNo AND approvedYN = 1 ), 0 ) as '{$string}',";
                }
                else {
                    $carryForwardLogic = '';

                    if ($policyMasterID == 1 ) {
                        $carryForwardLogic = ( $isCarryForward == 1 )? " ": " AND endDate BETWEEN '{$currentYearFirstDate}' AND '{$currentYearLastDate}'";
                    }

                    $select .= "sum(if(leaveType='{$lType}',daysEntitled,0)) as '{$string}',";
                    $select2 .= "sum( IF ( leaveType = '{$lType}', daysEntitled, 0 ) ) - 
	                             IFNULL( ( SELECT SUM( IF (leaveTypeID = '{$lType}', days, 0) ) FROM srp_erp_leavemaster WHERE 
	                             srp_erp_leavemaster.empID = empTB.EIdNo AND approvedYN = 1  {$carryForwardLogic} ), 0 ) AS '{$string}', ";


                }

            }


            /*$qry = "SELECT $select CONCAT(ECode, ' - ', Ename2) AS Ename2, srp_erp_leaveaccrualdetail.description, daysEntitled, srp_erp_leavetype.description AS leavetype,
                    srp_erp_leavetype.leaveTypeID, empID, confirmedYN, srp_erp_leavegroupdetails.policyMasterID,leaveaccrualDetailID,srp_erp_leaveaccrualmaster.confirmedYN,
                    srp_erp_leaveaccrualdetail.comment FROM srp_employeesdetails INNER JOIN (select * from `srp_erp_leavegroupdetails` WHERE leaveGroupID={$leaveGroupID} AND 
                    policyMasterID = {$policyMasterID}) srp_erp_leavegroupdetails ON srp_employeesdetails.leaveGroupID = srp_employeesdetails.leaveGroupID 
                    INNER JOIN srp_erp_leaveaccrualdetail ON empID = EIdNo AND srp_erp_leaveaccrualdetail.leaveType = srp_erp_leavegroupdetails.leaveTypeID 
                    INNER JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID 
                    AND srp_erp_leaveaccrualmaster.policyMasterID IN ($policyMasterID) 
                    INNER JOIN `srp_erp_leavetype` ON srp_erp_leavetype.leaveTypeID = srp_erp_leaveaccrualdetail.leaveType 
                    WHERE srp_erp_leaveaccrualdetail.leaveaccrualMasterID = $masterID AND srp_employeesdetails.leaveGroupID = {$leaveGroupID} GROUP BY empID ORDER BY Ename2";*/


            $qry = "SELECT $select CONCAT(ECode, ' - ', Ename2) AS Ename2, accDet.description, daysEntitled, empID,
                    leaveaccrualDetailID, accMaster.confirmedYN, accDet.leaveType, accDet.`comment`, accMaster.policyMasterID
                    FROM srp_employeesdetails empTB 
                    INNER JOIN srp_erp_leaveaccrualdetail accDet ON empID = EIdNo AND accDet.leaveaccrualMasterID={$masterID}
                    INNER JOIN srp_erp_leaveaccrualmaster accMaster ON accDet.leaveaccrualMasterID = accMaster.leaveaccrualMasterID 
                    AND accMaster.policyMasterID IN ($policyMasterID) 
                    INNER JOIN srp_erp_leavetype ON srp_erp_leavetype.leaveTypeID = accDet.leaveType 
                    WHERE accDet.leaveaccrualMasterID={$masterID}
                    GROUP BY empID ORDER BY Ename2";


            /*$qry2 = "SELECT $select2 CONCAT(ECode, ' - ', Ename2) AS Ename2, srp_erp_leaveaccrualdetail.description, daysEntitled, srp_erp_leavetype.description AS leavetype,
                     srp_erp_leavetype.leaveTypeID, empID, confirmedYN, srp_erp_leaveaccrualmaster.policyMasterID, leaveaccrualDetailID, srp_erp_leaveaccrualmaster.confirmedYN 
                     FROM srp_employeesdetails INNER JOIN srp_erp_leaveaccrualdetail on empID= EIdNo 
                     INNER JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID 
                     AND srp_erp_leaveaccrualmaster.policyMasterID ={$policyMasterID} 
                     INNER JOIN `srp_erp_leavetype` ON srp_erp_leavetype.leaveTypeID = srp_erp_leaveaccrualdetail.leaveType 
                     WHERE srp_employeesdetails.leaveGroupID = {$leaveGroupID} AND confirmedYN=1 GROUP BY empID ORDER BY Ename2";*/

            $qry2 = "SELECT {$select2} CONCAT(ECode, ' - ', Ename2) AS Ename2, entiDet.description, daysEntitled, srp_erp_leavetype.description AS leavetype, 
                     srp_erp_leavetype.leaveTypeID, empID, confirmedYN, entiMast.policyMasterID, leaveaccrualDetailID
                     FROM srp_employeesdetails AS empTB
                     INNER JOIN srp_erp_leaveaccrualdetail AS entiDet on empID= EIdNo
                     INNER JOIN srp_erp_leaveaccrualmaster AS entiMast ON entiDet.leaveaccrualMasterID = entiMast.leaveaccrualMasterID                     
                     AND entiMast.policyMasterID = {$policyMasterID} 
                     INNER JOIN `srp_erp_leavetype` ON srp_erp_leavetype.leaveTypeID = entiDet.leaveType
                     JOIN (
                        SELECT gMaster.leaveGroupID, leaveTypeID, policyMasterID, isCarryForward 
                        FROM srp_erp_leavegroup AS gMaster
                        JOIN srp_erp_leavegroupdetails AS gDet ON gMaster.leaveGroupID=gDet.leaveGroupID
                        WHERE companyID = {$companyID}
                     ) AS leaveTypeData ON leaveTypeData.leaveGroupID=entiMast.leaveGroupID  AND leaveTypeData.leaveTypeID = entiDet.leaveType
                     WHERE empTB.leaveGroupID = {$leaveGroupID} AND confirmedYN=1  AND 
                     CASE  WHEN ( leaveTypeData.isCarryForward=0 AND leaveTypeData.policyMasterID=1 ) 
                        THEN DATE_FORMAT( CONCAT(`year`,'-',LPAD(`month`,2,'00'),'-01'), '%Y-%m-%d') BETWEEN '{$currentYearFirstDate}' AND '{$currentYearLastDate}'
                        ELSE 1=1
                     END                                         
                     GROUP BY empID ORDER BY Ename2 ";

            $data['details'] = $this->db->query($qry)->result_array();
            $data['leaveHistory'] = $this->db->query($qry2)->result_array();
            //echo '<pre>'.$this->db->last_query().'</pre>';

        }

        $data['header'] = $header;
        $html = $this->load->view('system/hrm/leave_adjustment_detail_table', $data, TRUE);
        echo $html;
    }

    function leaveAdjustmentDetail_old()
    {
        $masterID = $this->input->post('masterID');


        $header = $this->db->query("SELECT srp_erp_leaveaccrualmaster.policyMasterID,srp_erp_leaveaccrualmaster.leaveGroupID,leaveType, srp_erp_leavetype.description FROM `srp_erp_leaveaccrualmaster` LEFT JOIN `srp_erp_leaveaccrualdetail` ON srp_erp_leaveaccrualmaster.leaveaccrualMasterID = srp_erp_leaveaccrualdetail.leaveaccrualMasterID LEFT JOIN `srp_erp_leavetype` ON srp_erp_leavetype.leaveTypeID = srp_erp_leaveaccrualdetail.leaveType WHERE srp_erp_leaveaccrualmaster.leaveaccrualMasterID = $masterID GROUP BY leaveType ORDER BY srp_erp_leavetype.description ASC")->result_array();

        $data['details'] = FALSE;
        $select = '';
        $select2 = '';
        if (!empty($header)) {
            foreach ($header as $val) {
                $policyMasterID = $val['policyMasterID'];
                $leaveGroupID = $val['leaveGroupID'];
                $string = str_replace(' ', '', $val['description']);
                if ($val['policyMasterID'] == 2) {
                    $select .= "sum(if(leaveType='{$val['leaveType']}',hoursEntitled,0)) as '{$string}',";
                    $select2 .= "sum(if(leaveType='{$val['leaveType']}',hoursEntitled,0)) - IFNULL( ( SELECT SUM(if(leaveTypeID='{$val['leaveType']}',hours,0)) FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.empID = srp_employeesdetails.EIdNo AND approvedYN = 1 ), 0 ) as '{$string}',";
                } else {
                    $select .= "sum(if(leaveType='{$val['leaveType']}',daysEntitled,0)) as '{$string}',";
                    $select2 .= "sum(if(leaveType='{$val['leaveType']}',daysEntitled,0)) - IFNULL( ( SELECT SUM(if(leaveTypeID='{$val['leaveType']}',days,0)) FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.empID = srp_employeesdetails.EIdNo AND approvedYN = 1 ), 0 ) as '{$string}',";
                }

            }


            $qry = "SELECT $select CONCAT(ECode, ' - ', Ename2) AS Ename2, srp_erp_leaveaccrualdetail.description, daysEntitled, srp_erp_leavetype.description AS leavetype,
                    srp_erp_leavetype.leaveTypeID, empID, confirmedYN, srp_erp_leavegroupdetails.policyMasterID,leaveaccrualDetailID,srp_erp_leaveaccrualmaster.confirmedYN,
                    srp_erp_leaveaccrualdetail.comment FROM srp_employeesdetails INNER JOIN (select * from `srp_erp_leavegroupdetails` WHERE leaveGroupID={$leaveGroupID} AND 
                    policyMasterID = {$policyMasterID}) srp_erp_leavegroupdetails ON srp_employeesdetails.leaveGroupID = srp_employeesdetails.leaveGroupID 
                    INNER JOIN srp_erp_leaveaccrualdetail ON empID = EIdNo AND srp_erp_leaveaccrualdetail.leaveType = srp_erp_leavegroupdetails.leaveTypeID 
                    INNER JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID 
                    AND srp_erp_leaveaccrualmaster.policyMasterID IN ($policyMasterID) 
                    INNER JOIN `srp_erp_leavetype` ON srp_erp_leavetype.leaveTypeID = srp_erp_leaveaccrualdetail.leaveType 
                    WHERE srp_erp_leaveaccrualdetail.leaveaccrualMasterID = $masterID AND srp_employeesdetails.leaveGroupID = {$leaveGroupID} GROUP BY empID ORDER BY Ename2";

            $qry2 = "SELECT $select2 CONCAT(ECode, ' - ', Ename2) AS Ename2, srp_erp_leaveaccrualdetail.description, daysEntitled, srp_erp_leavetype.description AS leavetype, 
                     srp_erp_leavetype.leaveTypeID, empID, confirmedYN, srp_erp_leaveaccrualmaster.policyMasterID, leaveaccrualDetailID, srp_erp_leaveaccrualmaster.confirmedYN 
                     FROM srp_employeesdetails INNER JOIN srp_erp_leaveaccrualdetail on empID= EIdNo 
                     INNER JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID 
                     AND srp_erp_leaveaccrualmaster.policyMasterID ={$policyMasterID} 
                     INNER JOIN `srp_erp_leavetype` ON srp_erp_leavetype.leaveTypeID = srp_erp_leaveaccrualdetail.leaveType 
                     WHERE srp_employeesdetails.leaveGroupID = {$leaveGroupID} AND confirmedYN=1 GROUP BY empID ORDER BY Ename2";

            $data['details'] = $this->db->query($qry)->result_array();
            // var_dump( $data['details']);
            $data['leaveHistory'] = $this->db->query($qry2)->result_array();
            //echo $this->db->last_query();
            //var_dump( $data['leaveHistory']);

        }

        $data['header'] = $header;
        $html = $this->load->view('system/hrm/leave_adjustment_detail_table', $data, TRUE);
        echo $html;
    }

    function update_leave_adjustment()
    {
        $masterID = $this->input->post('masterID');
        $days = $this->input->post('days');
        $leaveTypeID = $this->input->post('leaveTypeID');
        $empID = $this->input->post('empID');
        $policyMasterID = $this->input->post('policyMasterID');

        if ($policyMasterID == 2) {
            $data = array('hoursEntitled' => $days);
        } else {
            $data = array('daysEntitled' => $days);
        }

        $update = $this->db->update('srp_erp_leaveaccrualdetail', $data, array('leaveaccrualMasterID' => $masterID, 'leaveType' => $leaveTypeID, 'empID' => $empID));
        echo json_encode(array('error' => 0, 'Successfully saved'));

    }

    function delete_adjustmentDetail()
    {
        $masterID = $this->input->post('masterID');
        $empID = $this->input->post('empID');
        $this->db->delete('srp_erp_leaveaccrualdetail', array('leaveaccrualMasterID' => $masterID, 'empID' => $empID));
        echo json_encode(array('error' => 0, 'Successfully Deleted'));
    }

    function confirm_leaveadjustment()
    {
        $masterID = $this->input->post('masterID');

        $detail = $this->db->query("select * from srp_erp_leaveaccrualdetail WHERE leaveaccrualMasterID={$masterID} ")->row_array();
        if (!empty($detail)) {
            $data = array('confirmedYN' => 1, 'confirmedby' => $this->common_data['current_userID'], 'confirmedDate' => current_date());
            $update = $this->db->update('srp_erp_leaveaccrualmaster', $data, array('leaveaccrualMasterID' => $masterID));
            echo json_encode(array('error' => 0, 'message' => 'Successfully Confirmed'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Detail records not found. '));
        }
    }

    function delete_leaveAdjustment()
    {
        $masterID = $this->input->post('masterID');
        $this->db->delete('srp_erp_leaveaccrualdetail', array('leaveaccrualMasterID' => $masterID));
        $this->db->delete('srp_erp_leaveaccrualmaster', array('leaveaccrualMasterID' => $masterID));
        echo json_encode(array('error' => 0, 'Successfully Deleted'));
    }

    function savePayeeMaster()
    {

        $this->form_validation->set_rules('sortCode[]', 'Sort Code', 'required');
        $this->form_validation->set_rules('description[]', 'Description', 'required');
        $this->form_validation->set_rules('description[]', 'Description', 'required');
        $this->form_validation->set_rules('ifSlab[]', 'Slab', 'required');
        $this->form_validation->set_rules('payrollType[]', 'Payroll Type', 'required');

        $this->form_validation->set_rules('liabilityGlAutoID[]', 'Liability GL Code', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_payeeMaster());
        }
    }

    function fetch_payeeMaster()
    {
        $companyID = current_companyID();
        $this->datatables->select("  payeeMasterID, Description, sortCode, GLtbl2.systemAccountCode AS liablityGlCOde, liabilityGlAutoID AS liabilityGlAutoID,
              SlabID AS SlabID, isNonPayroll, IF(isNonPayroll='Y', 2 , 1) AS payrollYN")
            ->from('srp_erp_payeemaster AS t1')
            ->join('srp_erp_chartofaccounts GLtbl2', 't1.liabilityGlAutoID=GLtbl2.GLAutoID', 'left')
            ->add_column('isPayrollYN', '$1', 'isPayrollCategoryStr(payrollYN)')
            ->add_column('edit', '$1', 'action_payee(payeeMasterID,Description,sortCode,liabilityGlAutoID,SlabID,isNonPayroll)')
            ->where('t1.companyID', $companyID);

        echo $this->datatables->generate();
    }

    function editPayeeMaster()
    {
        $this->form_validation->set_rules('siSortCode', 'Social Insurance Sort Code', 'required');
        $this->form_validation->set_rules('siDes', 'Social Insurance Description', 'required');
        $this->form_validation->set_rules('si_liabilityGlAutoID', 'Liability GL Code', 'required');
        $this->form_validation->set_rules('siSlab', 'Slab', 'required');
        $this->form_validation->set_rules('hidden-id', 'Social Insurance ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->editPayeeMaster());
        }
    }

    function deletePayeeMaster()
    {
        $this->form_validation->set_rules('hidden-id', 'Payee Master ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->deletePayeeMaster());
        }
    }


    function getFormula()
    {
        $payGroupID = $this->input->post('payGroupID');
        $operand_arr = array('+', '*', '/', '-', '(', ')');
        $formulaText = '';
        $lastInputType = '';
        $salary_categories_arr = salary_categories(array('A', 'D'));

        $formulaDetails = $this->db->select('formula,formulaString,salaryCategories')->from('srp_erp_paygroupformula')->where('payGroupID', $payGroupID)->get()->row_array();
        $formula = $formulaDetails['formulaString'];
        $salaryCategories = $formulaDetails['salaryCategories'];

        if (!empty($formula) && $formula != null) {

            $formula_arr = explode('|', $formula); // break the formula

            foreach ($formula_arr as $formula_row) {

                if (trim($formula_row) != '') {
                    if (in_array($formula_row, $operand_arr)) { //validate is a operand
                        $formulaText .= $formula_row;
                        $lastInputType = 2;
                    } else {
                        $isNotCat = strpos($formula_row, '_'); // check is a amount

                        /********************************************************************************************
                         * If a amount remove '_' symbol and append in the formula
                         * else definitely its a salary category so get the description for the salaryCategoryID
                         ********************************************************************************************/
                        if ($isNotCat !== false) {
                            $numArr = explode('_', $formula_row);
                            $formulaText .= $numArr[1];
                            $formulaText .= (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];
                            $lastInputType = 0;
                        } else {

                            $keys = array_keys(array_column($salary_categories_arr, 'salaryCategoryID'), $formula_row);
                            $new_array = array_map(function ($k) use ($salary_categories_arr) {
                                return $salary_categories_arr[$k];
                            }, $keys);

                            $formulaText .= (!empty($new_array[0])) ? $new_array[0]['salaryDescription'] : ' &nbsp;&nbsp; ';
                            $lastInputType = 1;
                        }

                    }
                }

            }
        }
        echo json_encode(array('formulaText' => $formulaDetails['formula'], 'lastInputType' => $lastInputType, 'formula' => $formula, 'salaryCategories' => $salaryCategories));
    }


    /*function insert_default_dashboard_for_all_employee(){
        echo json_encode($this->Employee_model->insert_default_dashboard_for_all_employee());
    }*/

    public function load_empAccountsView()
    {
        //$docDet = $this->Employee_model->empDocument_setup();
        $id = $this->input->post('empID');
        $data['empID'] = $this->input->post('empID');
        $data['empDetail'] = $this->db->query("select ECode,Ename2 from srp_employeesdetails where EIdNo={$id} ")->row_array();
        $data['accountDetails'] = $this->Employee_model->loadEmpBankAccount($id);
        $data['accountDetails_nonPayroll'] = $this->Employee_model->loadEmpNonBankAccount($id);
        $this->load->view('system/hrm/ajax/load_empAccountsView', $data);
    }

    function update_attendance()
    {

        $companyID = current_companyID();
        $masterID = $this->input->post('masterID');
        $value = $this->input->post('value');
        $name = $this->input->post('name');
        $edit = false;

        $employee = $this->db->query("select empID from `srp_erp_pay_empattendancereview`  where ID={$masterID}")->row_array();


        switch ($name) {
            case "checkIn":
            case "checkOut":
                $value = ($value != '' ? date("H:i", strtotime($value)) : null);
                $edit = true;
                break;
            case "lateHours":
            case "earlyHours":
                $t = explode('_', $value);
                $hour = $t[0];
                $minutes = $t[1];
                $minutes = ($minutes == '' ? 0 : $minutes);
                if ($hour != '') {
                    $hour = $hour * 60;
                } else {
                    $hour = 0;
                }

                 $value = $hour + $minutes;

                $edit = false;
                break;
            case "OTHours":
            case "weekendOTHours":
            case "holidayOTHours":
            case "NDaysOT":
                $t = explode('_', $value);
                $hour = $t[0];
                $minutes = $t[1];
                $minutes = ($minutes == '' ? 0 : $minutes);
                if ($hour != '') {
                    $hour = $hour * 60;
                } else {
                    $hour = 0;
                }

                $value = $hour + $minutes;
                $edit = false;
                $update = $this->db->update('srp_erp_pay_empattendancereview', array('OTHours' => $value, 'weekendOTHours' => 0, 'holidayOTHours' => 0, 'NDaysOT' => 0), array('ID' => $masterID));
                if ($name == 'NDaysOT') {
                    $otMasterID = 1;
                }
                if ($name == 'weekendOTHours') {
                    $otMasterID = 2;
                }
                if ($name == 'holidayOTHours') {
                    $otMasterID = 3;
                }
                $otamount = $this->get_attendance_ot_amount($employee['empID'], $otMasterID);
                if ($update) {
                    if (!empty($otamount) && $otamount['transactionAmount'] > 0) {
                        $minuteOtAmount = ($otamount['transactionAmount'] / 60) * $value;

                    } else {
                        $minuteOtAmount = 0;
                    }
                    $update = $this->db->update('srp_erp_pay_empattendancereview', array('paymentOT' => $minuteOtAmount), array('ID' => $masterID));
                }
                break;

            case "normalDay":
            case "weekend":
            case "holiday":
                $update = $this->db->update('srp_erp_pay_empattendancereview', array('realTime' => $value, 'weekend' => 0, 'normalDay' => 0, 'holiday' => 0), array('ID' => $masterID));

                $value = $value;
                $edit = false;
                break;

            default:
                $value = $value;
                $edit = false;

        }
        $update = $this->db->update('srp_erp_pay_empattendancereview', array($name => $value), array('ID' => $masterID));
        if ($update) {
            if ($edit) {
                /*change in pulling date remove calender dauwekk ondition*/
                $qry = "SELECT srp_erp_pay_empattendancereview.*, shiftDet.isWeekend, IF(IFNULL(srp_erp_pay_empattendancereview.leaveMasterID, 0), 1, 0) AS isOnLeave, IF(IFNULL(holiday_flag, 0), 1, 0) AS holiday, mustCheck AS isCheckin, IF(IFNULL(isHalfDay, 0), 1, 0) AS isHalfDay FROM srp_erp_pay_empattendancereview LEFT JOIN (SELECT * FROM srp_erp_pay_shiftemployees WHERE companyID = {$companyID}) AS empShift ON empShift.empID = srp_erp_pay_empattendancereview.empID LEFT JOIN (SELECT * FROM srp_erp_pay_shiftdetails WHERE companyID = {$companyID}) AS shiftDet ON shiftDet.shiftID = empShift.shiftID AND shiftDet.weekDayNo = WEEKDAY(srp_erp_pay_empattendancereview.attendanceDate) LEFT JOIN (SELECT * FROM srp_erp_calender WHERE companyID = {$companyID}) AS calenders ON  fulldate = srp_erp_pay_empattendancereview.attendanceDate LEFT JOIN (SELECT leaveMasterID, empID, startDate, endDate FROM srp_erp_leavemaster WHERE companyID = {$companyID} AND approvedYN = 1) AS leaveExist ON leaveExist.empID = srp_erp_pay_empattendancereview.empID AND srp_erp_pay_empattendancereview.attendanceDate BETWEEN leaveExist.startDate AND leaveExist.endDate WHERE ID = {$masterID} AND srp_erp_pay_empattendancereview.companyID = {$companyID}";

                $detail = $this->db->query($qry)->row_array();

                if ($detail) {
                    $isAllSet = 0;
                    $workingHours = "";
                    $totWorkingHours = '';
                    $actualWorkingHours_obj = null;
                    $totWorkingHours_obj = null;
                    $realtime = null;
                    $AttPresentTypeID = '';
                    $lateHours = "";
                    $earlyHours = "";
                    $overTimeHours = 0;
                    $normaloverTimeHours = 0;
                    $weekendOTHours = 0;
                    $holidayoverTimeHours = 0;
                    $isCheckin = 0;
                    $isHalfDay = 1;
                    $normalrealtime = 0;
                    $weekendrealtime = 0;
                    $holidayrealtime = 0;


                    /**/

                    /**/

                    /************ Calculate the actual working hours *************/
                    if ($detail['onDuty'] != null && $detail['offDuty'] != null && $detail['checkOut'] != null) {
                        $datetime1 = new DateTime($detail['onDuty']);
                        $datetime2 = new DateTime($detail['offDuty']);
                        $actualWorkingHours_obj = $datetime1->diff($datetime2);
                        $minutes = $actualWorkingHours_obj->format('%i');
                        $hours = $actualWorkingHours_obj->format('%h');
                        $workingHours = ($hours * 60) + $minutes;
                    } else {
                        $isAllSet += 1;
                    }


                    /****** Employee total working hours for this day ******/
                    if ($detail['checkIn'] != null && $detail['checkOut'] != null) {

                        if ($detail['offDuty'] != '' && $detail['offDuty'] <= $detail['checkOut']) {
                            $datetime1 = new DateTime($detail['offDuty']);
                        } else {
                            $datetime1 = new DateTime($detail['checkOut']);
                        }


                        if ($detail['onDuty'] != '' && $detail['onDuty'] >= $detail['checkIn']) {
                            $datetime2 = new DateTime($detail['onDuty']);
                        } else {
                            $datetime2 = new DateTime($detail['checkIn']);
                        }

                        $totWorkingHours_obj = $datetime1->diff($datetime2);
                        $Hours = $totWorkingHours_obj->format('%h');
                        $minutes = $totWorkingHours_obj->format('%i');
                        $totWorkingHours = ($Hours * 60) + $minutes;

                        if ($workingHours != "" && $totWorkingHours != "") {
                            $realtime = $totWorkingHours / $workingHours;
                            $realtime = round($realtime, 1);
                        }


                    } else {
                        $isAllSet += 1;
                    }


                    if ($isAllSet == 0) {

                        /**** Calculation for late hours ****/
                        $clockIn_datetime = new DateTime($detail['checkIn']);
                        $onDuty_datetime = new DateTime($detail['onDuty']);


                        if ($clockIn_datetime->format('H:i:s') > $onDuty_datetime->format('H:i:s')) {
                            $interval = $clockIn_datetime->diff($onDuty_datetime);

                            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
                            $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
                             $lateHours = $hours * 60 + $minutes;
                        }


                        /**** Calculation for early hours ****/
                        $datetime1 = date('Y-m-d H:i:s', strtotime($detail['checkOut']));
                        $datetime2 = date('Y-m-d H:i:s', strtotime($detail['offDuty']));
                        if ($datetime1 < $datetime2) {
                            $datetime1 = new DateTime($detail['checkOut']);
                            $datetime2 = new DateTime($detail['offDuty']);
                            $interval = $datetime2->diff($datetime1);
                            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
                            $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
                            $earlyHours = $hours * 60 + $minutes;

                        }


                        /*ot shahmy */

                        /**/
                        $clockouttime = date('Y-m-d H:i:s', strtotime($detail['checkOut']));
                        $offduty = date('Y-m-d H:i:s', strtotime($detail['offDuty']));

                        if ($clockouttime > $offduty) {


                            $Fdate = date('Y-m-d');
                            if ($detail['onDuty'] >= $detail['checkIn']) {
                                $onDutyForOT = new DateTime($detail['onDuty']);
                            } else {
                                $onDutyForOT = new DateTime($detail['checkIn']);
                            }

                            $clockOutForOT = new DateTime($detail['checkOut']);
                            $workingHours_obj = $onDutyForOT->diff($clockOutForOT);
                            $totW = new DateTime($workingHours_obj->format('' . $Fdate . ' %h:%i:%s'));
                            $actW = new DateTime($actualWorkingHours_obj->format('' . $Fdate . ' %h:%i:%s'));

                            $worktime= $totW->format('' . $Fdate . ' h:i:s');

                            $actualtime= $actW->format('' . $Fdate . ' h:i:s');
                           if($worktime <= $actualtime){
                               $overTimeHours = 0;
                           }else{
                               $overTime_obj = $actW->diff($totW);
                               $hours = ($overTime_obj->format('%h') != 0) ? $overTime_obj->format('%h') : 0;
                               $minutes = ($overTime_obj->format('%i') != 0) ? $overTime_obj->format('%i') : 0;
                               $overTimeHours = $hours * 60 + $minutes;
                           }


                        }


                        /*     if ($actualWorkingHours_obj->format('%h %i') < $totWorkingHours_obj->format('%h %i')) {

                                 $onDutyForOT = new DateTime($detail['onDuty']);
                                 $clockOutForOT = new DateTime($detail['checkOut']);
                                 $workingHours_obj = $onDutyForOT->diff($clockOutForOT);

                                 $Fdate = date('Y-m-d');

                                 $totW = new DateTime($workingHours_obj->format('' . $Fdate . ' %h:%i:%s'));
                                 $actW = new DateTime($actualWorkingHours_obj->format('' . $Fdate . ' %h:%i:%s'));
                                 $overTime_obj = $actW->diff($totW);
                                 $hours = ($overTime_obj->format('%h') != 0) ? $overTime_obj->format('%h') : 0;
                                 $minutes = ($overTime_obj->format('%i') != 0) ? $overTime_obj->format('%i') : 0;
                                 $overTimeHours = $hours * 60 + $minutes;


                             }*/
                    }


                    if ($detail['checkIn'] == null && $detail['checkOut'] == null) {
                        $AttPresentTypeID = 4;
                        /**** Absents *****/
                    } else {
                        $clockIn_datetime = date('Y-m-d H:i:s', strtotime($detail['checkIn']));
                        $onDuty_datetime = date('Y-m-d H:i:s', strtotime($detail['onDuty']));
                        if ($clockIn_datetime <= $onDuty_datetime) {
                            $AttPresentTypeID = 1;
                        } /**** Presented On time *****/
                        elseif ($clockIn_datetime > $onDuty_datetime) {
                            $AttPresentTypeID = 2;
                        } /**** Presented Later*****/
                        else {
                            $AttPresentTypeID = '';
                        }
                        /***** Let the user decide ****/
                    }

                    if ($detail['isOnLeave'] == 1) {
                        $AttPresentTypeID = 5;
                    }
                    /**** Employee On Leave *****/


                    $normaloverTimeHours = $overTimeHours;
                    $normalrealtime = $realtime;


                    if ($detail['isWeekend'] == 1) {
                        /**/
                        if ($detail['checkIn'] != null || $detail['checkOut'] != null) {
                            $AttPresentTypeID = 1;
                        }
                        $overTimeHours = $totWorkingHours;
                        /**/
                        $normaloverTimeHours = 0;
                        $weekendOTHours = $totWorkingHours;

                        $normalrealtime = 0;
                        $weekendrealtime = $realtime;


                    }
                    $attendhours=0;
                    if ($detail['holiday'] == 1) {
                        /*2018-11-07*/
                        if ($detail['checkIn'] != null && $detail['checkOut'] != null) {
                            $datetime1 = new DateTime($detail['checkIn']);
                            $datetime2 = new DateTime($detail['checkOut']);
                            $attendhours_obj = $datetime1->diff($datetime2);
                            $Hours = $attendhours_obj->format('%h');
                            $minutes = $attendhours_obj->format('%i');
                            $attendhours = ($Hours * 60) + $minutes;

                        }
                        if ($detail['checkIn'] != null || $detail['checkOut'] != null) {
                            $AttPresentTypeID = 1;
                        }
                        $overTimeHours = $attendhours;
                        /**/
                        $normaloverTimeHours = 0;
                        $weekendOTHours = 0;
                        $holidayoverTimeHours = $attendhours;
                        $normalrealtime = 0;
                        $weekendrealtime = 0;
                        $holidayrealtime = $realtime;
                    }

                    if ($detail['isCheckin'] == 1) {
                        $isCheckin = 1;
                    }

                    if ($detail['isHalfDay'] == 1) {
                        $isHalfDay = 0.5;
                    }


                    $details = array(
                        'checkIn' => $detail['checkIn'],
                        'checkOut' => $detail['checkOut'],
                        'presentTypeID' => $AttPresentTypeID,
                        'lateHours' => $lateHours,
                        'earlyHours' => $earlyHours,
                        'OTHours' => $overTimeHours,
                        'weekendOTHours' => $weekendOTHours,
                        'mustCheck' => $isCheckin,
                        'normalTime' => $isHalfDay,
                        'realTime' => $realtime,
                        'NDaysOT' => $normaloverTimeHours,
                        'holidayOTHours' => $holidayoverTimeHours,
                        'normalDay' => $normalrealtime,
                        'weekend' => $weekendrealtime,
                        'holiday' => $holidayrealtime,

                    );


                }


                $this->db->update('srp_erp_pay_empattendancereview', $details, array('ID' => $masterID));


                /**/

            }

            $result = $this->db->query("select * from srp_erp_pay_empattendancereview where ID={$masterID}")->row_array();

            $lateHoursarr = array('h' => gmdate("H", $result['lateHours'] * 60), 'm' => gmdate("i", $result['lateHours'] * 60));
            $earlyHoursarr = array('h' => gmdate("H", $result['earlyHours'] * 60), 'm' => gmdate("i", $result['earlyHours'] * 60));
            $OTHoursarr = array('h' => gmdate("H", $result['OTHours'] * 60), 'm' => gmdate("i", $result['OTHours'] * 60));
            $weekendOTHoursarr = array('h' => gmdate("H", $result['weekendOTHours'] * 60), 'm' => gmdate("i", $result['weekendOTHours'] * 60));
            $holidayOTHoursarr = array('h' => gmdate("H", $result['holidayOTHours'] * 60), 'm' => gmdate("i", $result['holidayOTHours'] * 60));
            $NDaysOTsarr = array('h' => gmdate("H", $result['NDaysOT'] * 60), 'm' => gmdate("i", $result['NDaysOT'] * 60));

            $totWorkingHours = 0;
            $attendhours = '';
            $isAllSet = 0;
            if ($result['checkIn'] != null && $result['checkOut'] != null && $result['offDuty'] != null) {


                if ($result['offDuty'] <= $result['checkOut']) {
                    $datetime1 = new DateTime($result['offDuty']);
                } else {
                    $datetime1 = new DateTime($result['checkOut']);
                }
                if ($result['onDuty'] >= $result['checkIn']) {
                    $datetime2 = new DateTime($result['onDuty']);
                } else {
                    $datetime2 = new DateTime($result['checkIn']);
                }
                $totWorkingHours_obj = $datetime1->diff($datetime2);
                $totWorkingHours = $totWorkingHours_obj->format('%h') . " h &nbsp;&nbsp;" . $totWorkingHours_obj->format('%i') . " m";
            }
            if ($result['checkIn'] != null && $result['checkOut'] != null) {
                $datetime1 = new DateTime($result['checkIn']);
                $datetime2 = new DateTime($result['checkOut']);
                $attendhours_obj = $datetime1->diff($datetime2);
                $attendhours = $attendhours_obj->format('%h') . " h &nbsp;&nbsp;" . $attendhours_obj->format('%i') . " m";
            }

            $data = array('realTime' => $result['realTime'],
                'presentTypeID' => $result['presentTypeID'],
                'weekend' => $result['weekend'],
                'holiday' => $result['holiday'],
                'normalDay' => $result['normalDay'],
                'h_lateHours' => gmdate("H", $result['lateHours'] * 60),
                'm_lateHours' => gmdate("i", $result['lateHours'] * 60),
                'h_earlyHours' => gmdate("H", $result['earlyHours'] * 60),
                'm_earlyHours' => gmdate("i", $result['earlyHours'] * 60),
                'h_OTHours' => gmdate("H", $result['OTHours'] * 60),
                'm_OTHours' => gmdate("i", $result['OTHours'] * 60),
                'h_weekendOTHours' => gmdate("H", $result['weekendOTHours'] * 60),
                'm_weekendOTHours' => gmdate("i", $result['weekendOTHours'] * 60),
                'h_holidayOTHours' => gmdate("H", $result['holidayOTHours'] * 60),
                'm_holidayOTHours' => gmdate("i", $result['holidayOTHours'] * 60),
                'h_NDaysOT' => gmdate("H", $result['NDaysOT'] * 60),
                'm_NDaysOT' => gmdate("i", $result['NDaysOT'] * 60),
                'totWorkingHours' => $totWorkingHours,
                'attendhours' => $attendhours,
                'paymentOT' => round($result['paymentOT'], 2)

            );


            echo json_encode(array('error' => 0, 'message' => 'Updated Successfully', 'data' => $data));

        } else {
            echo json_encode(array('error' => 1, 'message' => 'Failed'));
        }
    }

    function get_attendance_ot_amount($empID, $OTMasterID)
    {

        $companyID = current_companyID();
        $detail_arr = $this->db->query("SELECT overTimeGroup, srp_erp_pay_overtimegroupdetails.*, srp_erp_pay_overtimecategory.* FROM `srp_employeesdetails` INNER JOIN `srp_erp_pay_overtimegroupdetails` ON groupID = overTimeGroup INNER JOIN `srp_erp_pay_overtimecategory` ON overTimeID = ID WHERE EidNo = '{$empID}'")->result_array();


        if ($detail_arr) {
            foreach ($detail_arr as $key => $row) {

                if ($row['OTMasterID'] == $OTMasterID) {
                    $classTitle = explode(' ', $row['description']);
                    $formulaText = '';
                    $formula = trim($row['formula']);
                    $lastInputType = '';
                    $formulaBuilder = $this->formulaBuilder_to_sql_OT($formula);
                    $formulaDecodeOT = $formulaBuilder['formulaDecode'];
                    $select_str2 = $formulaBuilder['select_str2'];
                    $whereInClause = $formulaBuilder['whereInClause'];

                    $as = $this->db->query("
                                   SELECT  calculationTB.employeeNo, 'G',
                                   (({$formulaDecodeOT } ) )AS transactionAmount, transactionCurrencyID, transactionCurrency, transactionER,
                                   transactionCurrencyDecimalPlaces,
                                   round( ((" . $formulaDecodeOT . " ) / companyLocalER) , companyLocalCurrencyDecimalPlaces  )AS localAmount,
                                   companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                   round( ((" . $formulaDecodeOT . " ) / companyReportingER)   , companyReportingCurrencyDecimalPlaces  )AS reportingAmount,
                                   companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces,                               
                                   seg.segmentID, seg.segmentCode
                                   FROM (
                                        SELECT employeeNo, " . $select_str2 . " ,
                                        transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces,
                                        companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                        companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces
                                        FROM srp_erp_pay_salarydeclartion AS salDec
                                        JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID = salDec.salaryCategoryID AND salCat.companyID ={$companyID}
                                        WHERE salDec.companyID = {$companyID}  AND employeeNo={$empID} 
                                        AND salDec.salaryCategoryID  IN (" . $whereInClause . ") GROUP BY employeeNo, salDec.salaryCategoryID
                                   ) calculationTB
                                   JOIN srp_employeesdetails AS emp ON emp.EIdNo = calculationTB.employeeNo AND emp.Erp_companyID = {$companyID}
                                   JOIN srp_erp_segment seg ON seg.segmentID = emp.segmentID AND seg.companyID = {$companyID}
                                   GROUP BY employeeNo")->row_array();

                }

            }
        }

        return $as;
    }

    function formulaBuilder_to_sql_OT($formula)
    {

        $salary_categories_arr = salary_categories(array('A', 'D'));
        $formulaText = '';
        $salaryCatID = array();
        $formulaDecode_arr = array();
        $operand_arr = operand_arr();

        $formula_arr = explode('|', $formula); // break the formula

        $n = 0;
        foreach ($formula_arr as $formula_row) {

            if (trim($formula_row) != '') {
                if (in_array($formula_row, $operand_arr)) { //validate is a operand
                    $formulaText .= $formula_row;
                    $formulaDecode_arr[] = $formula_row;
                } else {

                    $elementType = $formula_row[0];

                    if ($elementType == '_') {
                        /*** Number ***/
                        $numArr = explode('_', $formula_row);
                        $formulaText .= (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];
                        $formulaDecode_arr[] = (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];

                    } else if ($elementType == '#') {
                        /*** Salary category ***/
                        $catArr = explode('#', $formula_row);
                        $salaryCatID[$n]['ID'] = $catArr[1];

                        $keys = array_keys(array_column($salary_categories_arr, 'salaryCategoryID'), $catArr[1]);
                        $new_array = array_map(function ($k) use ($salary_categories_arr) {
                            return $salary_categories_arr[$k];
                        }, $keys);

                        $salaryDescription = (!empty($new_array[0])) ? trim($new_array[0]['salaryDescription']) : '';

                        $formulaText .= $salaryDescription;

                        $salaryDescription_arr = explode(' ', $salaryDescription);
                        $salaryDescription_arr = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescription_arr);
                        $salaryCatID[$n]['cat'] = implode('_', $salaryDescription_arr) . '_' . $n;
                        $formulaDecode_arr[] = 'SUM(' . $salaryCatID[$n]['cat'] . ')';

                        $n++;

                    }
                }
            }

        }

        $formulaDecode = implode(' ', $formulaDecode_arr);

        $select_str2 = '';
        $whereInClause = '';
        $separator = '';

        foreach ($salaryCatID as $key1 => $row) {
            $select_str2 .= $separator . 'IF(salDec.salaryCategoryID=' . $row['ID'] . ', SUM(transactionAmount) , 0 ) AS ' . $row['cat'] . '';
            $whereInClause .= $separator . ' ' . $row['ID'];
            $separator = ',';
        }

        return array(
            'formulaDecode' => $formulaDecode,
            'select_str2' => $select_str2,
            'whereInClause' => $whereInClause,
        );

    }

    function delete_attendance()
    {
        $masterID = $this->input->post('masterID');

        $delete = $this->db->delete('srp_erp_pay_empattendancereview', array('ID' => $masterID));

        if ($delete) {
            echo json_encode(array('error' => 0, 'message' => 'Updated Successfully'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Failed.'));
        }

    }

    function load_declaration_drilldown_table()
    {

        echo json_encode($this->Employee_model->load_declaration_drilldown_table());

    }

    function attendance_confirmation()
    {
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        $companyID = current_companyID();
        $otarray = array();

        $qry = "SELECT ID,empID, NDaysOT, weekendOTHours, holidayOTHours, overTimeGroup, (CASE WHEN NDaysOT > 0  THEN 1 WHEN weekendOTHours > 0  THEN 2 WHEN holidayOTHours > 0 THEN 3 ELSE 0 END) AS OtmasterID FROM `srp_erp_pay_empattendancereview` LEFT JOIN `srp_employeesdetails` ON EIdNo = empID WHERE (attendanceDate BETWEEN '{$fromDate}' AND '{$toDate}') AND confirmedYN = 0 AND companyID = {$companyID} AND (NDaysOT > 0 OR weekendOTHours > 0 OR holidayOTHours > 0) AND overTimeGroup > 0";
        $ot = $this->db->query($qry)->result_array();
        if ($ot) {
            foreach ($ot as $employee) {
                $otamount = $this->get_attendance_ot_amount($employee['empID'], $employee['OtmasterID']);

                if ($employee['OtmasterID'] == 1) {
                    $value = $employee['NDaysOT'];
                }
                if ($employee['OtmasterID'] == 2) {
                    $value = $employee['weekendOTHours'];
                }

                if ($employee['OtmasterID'] == 3) {
                    $value = $employee['holidayOTHours'];
                }
                if (!empty($otamount) && $otamount['transactionAmount'] > 0) {
                    $minuteOtAmount = ($otamount['transactionAmount'] / 60) * $value;

                } else {
                    $minuteOtAmount = 0;
                }


                array_push($otarray, array('paymentOT' => $minuteOtAmount, 'ID' => $employee['ID']));
            }
        }


        $validation = $this->db->query("SELECT COUNT(empID) AS count,Ename2 FROM `srp_erp_pay_empattendancereview` LEFT JOIN `srp_employeesdetails` on EIdNo=empID WHERE (attendanceDate BETWEEN '{$fromDate}' AND '{$toDate}') AND confirmedYN=0 AND companyID={$companyID} GROUP BY EmpID , attendanceDate,srp_erp_pay_empattendancereview.floorID HAVING count > 1")->result_array();
        if (!empty($validation)) {
            $last_names = array_column($validation, 'Ename2');
            $error = join(" , ", $last_names);
            echo json_encode(array('error' => 1, 'message' => 'Duplicate attendance found for ' . $error));
            exit;
        }

        $data = array(
            'confirmedYN' => 1,
            'confirmedBy' => $this->common_data['current_userID'],
            'confirmedDate' => date('Y-m-d'),
        );
        $this->db->where("(attendanceDate BETWEEN '{$fromDate}' AND '{$toDate}') ");
        $this->db->where("confirmedYN", 0);
        $this->db->where("companyID", $companyID);
        $update = $this->db->update("srp_erp_pay_empattendancereview", $data);

        if ($update) {
            if (!empty($otarray)) {
                $this->db->update_batch('srp_erp_pay_empattendancereview', $otarray, 'ID');
            }
            echo json_encode(array('error' => 0, 'message' => 'Successfully confirmed '));

        } else {
            echo json_encode(array('error' => 1, 'message' => 'Failed'));
        }
    }

    function attendanceMachineTable()
    {

        $asofDate = $this->input->post('asofDate');
        $filterDepartment = $this->input->post('filterDepartment');
        $companyID = current_companyID();
        $where = "srp_erp_pay_empattendancereview.companyID =$companyID AND confirmedYN=1 ";
        if ($asofDate != '') {
            $where .= " AND attendanceDate <='{$asofDate}'";
        }
        if ($filterDepartment != '') {
            $where .= " AND srp_erp_pay_empattendancereview.floorID={$filterDepartment}";
        }


        $select = "t.floorDescription AS floorDescription, t.attendanceDate AS attendanceDate, t.floorID AS floorID, t.confirmedYN AS confirmedYN, CASE WHEN t.total = t.approved THEN 1 WHEN t.total = t.NotApproved THEN 0 ELSE 2 END AS approvedYN";

        $from = " (SELECT floorDescription, attendanceDate, srp_erp_pay_empattendancereview.floorID AS floorID, confirmedYN,COUNT(ID) AS total, COUNT(CASE WHEN approvedYN = 1 THEN 1 ELSE NULL END) AS approved, COUNT(CASE WHEN approvedYN = 0 THEN 1 ELSE NULL END) AS NotApproved FROM srp_erp_pay_empattendancereview LEFT JOIN srp_erp_pay_floormaster ON srp_erp_pay_floormaster.floorID = srp_erp_pay_empattendancereview.floorID WHERE $where GROUP BY attendanceDate , srp_erp_pay_empattendancereview.floorID) t";

        $this->datatables->select($select, false)
            ->from($from)
            /*->join('srp_erp_pay_floormaster', 'srp_erp_pay_floormaster.floorID=srp_erp_pay_empattendancereview.floorID', 'left')*/
            ->add_column('edit', '<a onclick="edit_attendance(\'$1\',$2)"><span title="View" rel="tooltip" class="fa fa-fw fa-eye"></span>', 'attendanceDate,floorID')
            ->add_column('confirm', '$1', 'confirm(confirmedYN)')
            ->add_column('approvedYN', '$1', 'confirm(approvedYN)');


        /* $this->datatables->select("floorDescription, attendanceDate,srp_erp_pay_empattendancereview.floorID as floorID,confirmedYN,approvedYN", false)->from('srp_erp_pay_empattendancereview')->join('srp_erp_pay_floormaster', 'srp_erp_pay_floormaster.floorID=srp_erp_pay_empattendancereview.floorID', 'left')->add_column('edit', '<a onclick="edit_attendance(\'$1\',$2)"><span class="fa fa-fw fa-eye"></span>', 'attendanceDate,floorID')->add_column('confirm', '$1', 'confirm(confirmedYN)')->add_column('approvedYN', '$1', 'confirm(approvedYN)')->where($where)->group_by('attendanceDate,srp_erp_pay_empattendancereview.floorID');*/

        echo $this->datatables->generate();
    }

    function attendanceMachineTableApproval()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $groupby = $this->input->post('groupby');
        $approvedYN = $this->input->post('approvedYN');
        $filterDepartment = $this->input->post('filterDepartment');
        $managerID = current_userID();

        $hrAdmin = $this->db->query("Select * from srp_employeesdetails where isHRAdmin=1 AND EIdNo={$managerID}")->row_array();


        $companyID = current_companyID();
        $where = "srp_erp_pay_empattendancereview.companyID =$companyID AND confirmedYN=1  ";
        $filter = ($approvedYN == 0 ? "  approvedYN !=1" : "  approvedYN = 1 ");
        $where .= ($filterDepartment != "" ? " AND srp_erp_pay_empattendancereview.floorID={$filterDepartment}" : "");
        if (empty($hrAdmin)) {
            $where .= " AND srp_erp_employeemanagers.level=0 AND srp_erp_employeemanagers.active=1 AND managerID={$managerID}";
        }
        $where .= ($datefrom != "" ? " AND attendanceDate >='{$datefrom}'" : "");
        $where .= ($dateto != "" ? " AND attendanceDate <='{$dateto}'" : "");

        $group_by = ($groupby == "0" ? "attendanceDate,srp_erp_pay_empattendancereview.floorID" : "srp_erp_pay_empattendancereview.empID");

        $select = ($groupby == "0" ? "attendanceDate" : "srp_employeesdetails.Ename2 as attendanceDate");
        $select2 = ($groupby == "0" ? "attendanceDate" : "t.Ename2 as attendanceDate");


        $edit = ($groupby == "0" ? "attendanceDate,floorID,attDate" : "empID,floorID,employee");

        /*echo $group_by;
        echo $where;*/


        $first = "(select CASE WHEN t.total = t.approved THEN 1 WHEN t.total = t.NotApproved THEN 0 ELSE 2 END AS approvedYN, t.floorID as floorID , t.confirmedYN, empID, floorDescription,$select2";

        $second = "from (SELECT srp_erp_pay_empattendancereview.floorID, confirmedYN, EIdNo AS empID, floorDescription,$select, COUNT(ID) AS total, COUNT(CASE WHEN approvedYN = 1 THEN 1 ELSE NULL END) AS approved, COUNT(CASE WHEN approvedYN = 0 THEN 1 ELSE NULL END) AS NotApproved FROM srp_erp_pay_empattendancereview LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_pay_empattendancereview.empID LEFT JOIN srp_erp_pay_floormaster ON srp_erp_pay_floormaster.floorID = srp_erp_pay_empattendancereview.floorID INNER JOIN srp_erp_employeemanagers ON srp_erp_employeemanagers.empID = srp_erp_pay_empattendancereview.empID WHERE $where GROUP BY $group_by) t )";


        if (empty($hrAdmin)) {
            $this->datatables->select("attendanceDate,approvedYN, floorID, confirmedYN, empID, floorDescription", false)->from("(select CASE WHEN t.total = t.approved THEN 1 WHEN t.total = t.NotApproved THEN 0 ELSE 2 END AS approvedYN, t.floorID as floorID , t.confirmedYN, empID, floorDescription,$select2 from (SELECT srp_erp_pay_empattendancereview.floorID, confirmedYN, EIdNo AS empID, floorDescription,$select, COUNT(ID) AS total, COUNT(CASE WHEN approvedYN = 1 THEN 1 ELSE NULL END) AS approved, COUNT(CASE WHEN approvedYN = 0 THEN 1 ELSE NULL END) AS NotApproved FROM srp_erp_pay_empattendancereview LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_pay_empattendancereview.empID LEFT JOIN srp_erp_pay_floormaster ON srp_erp_pay_floormaster.floorID = srp_erp_pay_empattendancereview.floorID INNER JOIN srp_erp_employeemanagers ON srp_erp_employeemanagers.empID = srp_erp_pay_empattendancereview.empID WHERE $where GROUP BY $group_by) t ) a")
                ->where($filter)
                ->add_column('edit', '<a onclick="edit_attendance(\'$1\',$2,\'$3\')"><span title="View" rel="tooltip" class="fa fa-check"></span>', $edit)
                ->add_column('confirm', '$1', 'confirm(confirmedYN)')
                ->add_column('approved', '$1', 'confirm(approvedYN)');
        } else {
            $this->datatables->select("attendanceDate,approvedYN, floorID, confirmedYN, empID, floorDescription", false)->from("(select CASE WHEN t.total = t.approved THEN 1 WHEN t.total = t.NotApproved THEN 0 ELSE 2 END AS approvedYN, t.floorID as floorID , t.confirmedYN, empID, floorDescription,$select2 from (SELECT srp_erp_pay_empattendancereview.floorID, confirmedYN, EIdNo AS empID, floorDescription,$select, COUNT(ID) AS total, COUNT(CASE WHEN approvedYN = 1 THEN 1 ELSE NULL END) AS approved, COUNT(CASE WHEN approvedYN = 0 THEN 1 ELSE NULL END) AS NotApproved FROM srp_erp_pay_empattendancereview LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_pay_empattendancereview.empID LEFT JOIN srp_erp_pay_floormaster ON srp_erp_pay_floormaster.floorID = srp_erp_pay_empattendancereview.floorID  WHERE $where GROUP BY $group_by) t ) a")
                ->where($filter)
                ->add_column('edit', '<a onclick="edit_attendance(\'$1\',$2,\'$3\')"><span title="View" rel="tooltip" class="fa fa-fw fa-eye"></span>', $edit)
                ->add_column('confirm', '$1', 'confirm(confirmedYN)')
                ->add_column('approved', '$1', 'confirm(approvedYN)');
        }


        echo $this->datatables->generate();
    }

    function machineattendanceView()
    {
        $hideedit = false;
        $hideedit = $this->input->post('hideedit');

        $companyID = current_companyID();

        $attendanceDate = $this->input->post('attendanceDate');
        $approvedYN = $this->input->post('approvedYN');

        $companyID = current_companyID();
        $qry = "SELECT isWeekEndDay,approvedComment,approvedYN,empID,ECode,Ename1, Ename2,empMachineID, floorDescription, ID, machineID, srp_erp_pay_empattendancereview.floorID, attendanceDate, presentTypeID,   DATE_FORMAT(checkIn, '%h:%i %p') checkIn, DATE_FORMAT(checkOut, '%h:%i %p') checkOut, DATE_FORMAT(onDuty, '%h:%i %p') onDuty, DATE_FORMAT(offDuty, '%h:%i %p') offDuty, lateHours, earlyHours, OTHours,normalDay,mustCheck,normalTime, weekend, holiday, NDaysOT, weekendOTHours, holidayOTHours,realTime FROM srp_erp_pay_empattendancereview LEFT JOIN `srp_employeesdetails` ON srp_erp_pay_empattendancereview.empID = srp_employeesdetails.EIdNo LEFT JOIN `srp_erp_pay_floormaster` ON srp_erp_pay_floormaster.floorID = srp_erp_pay_empattendancereview.floorID WHERE attendanceDate  = '{$attendanceDate}' AND srp_erp_pay_empattendancereview.companyID = {$companyID} AND confirmedYN=1";
        $data['tempAttData'] = $this->db->query($qry)->result_array();
        $data['hideedit'] = $hideedit;
        echo $this->load->view('system/hrm/ajax/attendanceListView', $data, true);
    }

    function AttendanceApprovalList()
    {
        $hideedit = false;
        $hideedit = $this->input->post('hideedit');

        $companyID = current_companyID();

        $attendanceDate = $this->input->post('attendanceDate');
        $approvedYN = $this->input->post('approvedYN');
        $managerId = current_userID();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $companyID = current_companyID();
        $floorID = $this->input->post('floorID');


        $where = "";
        $where .= ($approvedYN != '' ? " AND approvedYN ={$approvedYN}" : "");
        $where .= ($datefrom != '' ? " AND attendanceDate >='{$datefrom}'" : "");
        $where .= ($dateto != '' ? " AND attendanceDate <='{$dateto}'" : "");
        /*        $qry = "SELECT approvedYN,empID,ECode,Ename1, Ename2,empMachineID, floorDescription, ID, machineID, srp_erp_pay_empattendancereview.floorID, attendanceDate, presentTypeID,   DATE_FORMAT(checkIn, '%h:%i %p') checkIn, DATE_FORMAT(checkOut, '%h:%i %p') checkOut, DATE_FORMAT(onDuty, '%h:%i %p') onDuty, DATE_FORMAT(offDuty, '%h:%i %p') offDuty, lateHours, earlyHours, OTHours, mustCheckIn, mustCheckOut, weekend, holiday, NDaysOT, weekendOTHours, holidayOTHours,realTime FROM srp_erp_pay_empattendancereview LEFT JOIN `srp_employeesdetails` ON srp_erp_pay_empattendancereview.empID = srp_employeesdetails.EIdNo LEFT JOIN `srp_erp_pay_floormaster` ON srp_erp_pay_floormaster.floorID = srp_erp_pay_empattendancereview.floorID WHERE attendanceDate  = '{$attendanceDate}' AND srp_erp_pay_empattendancereview.companyID = {$companyID} AND confirmedYN=1";*/
        $managerID = current_userID();

        $hrAdmin = $this->db->query("Select * from srp_employeesdetails where isHRAdmin=1 AND EIdNo={$managerID}")->row_array();
        if (empty($hrAdmin)) {
            $qry = "SELECT ROUND(noPayAmount, 2) as noPayAmount,ROUND(noPaynonPayrollAmount, 2) as noPaynonPayrollAmount,isWeekEndDay,ROUND(paymentOT, 2) as paymentOT,approvedComment,approvedYN, srp_erp_pay_empattendancereview.empID, ECode, Ename1, Ename2, empMachineID, floorDescription, ID, machineID, srp_erp_pay_empattendancereview.floorID, attendanceDate, presentTypeID, DATE_FORMAT(checkIn, '%h:%i %p') checkIn, DATE_FORMAT(checkOut, '%h:%i %p') checkOut, DATE_FORMAT(onDuty, '%h:%i %p') onDuty, DATE_FORMAT(offDuty, '%h:%i %p') offDuty, lateHours, earlyHours, OTHours, mustCheck, normalTime,normalDay, weekend, holiday, NDaysOT, weekendOTHours, holidayOTHours, realTime FROM srp_erp_pay_empattendancereview LEFT JOIN `srp_employeesdetails` ON srp_erp_pay_empattendancereview.empID = srp_employeesdetails.EIdNo LEFT JOIN `srp_erp_pay_floormaster` ON srp_erp_pay_floormaster.floorID = srp_erp_pay_empattendancereview.floorID INNER JOIN `srp_erp_employeemanagers` on srp_erp_employeemanagers.empID=srp_employeesdetails.EIdNo WHERE attendanceDate = '{$attendanceDate}' AND srp_erp_pay_empattendancereview.companyID = {$companyID} AND confirmedYN = 1 AND srp_erp_employeemanagers.level=0 AND srp_erp_employeemanagers.active=1 AND managerID={$managerId} AND srp_erp_pay_empattendancereview.floorID=$floorID";
        } else {
            /*hradmin*/
            $qry = "SELECT ROUND(noPayAmount, 2) as noPayAmount,ROUND(noPaynonPayrollAmount, 2) as noPaynonPayrollAmount,isWeekEndDay,ROUND(paymentOT, 2) as paymentOT,approvedComment,approvedYN, srp_erp_pay_empattendancereview.empID, ECode, Ename1, Ename2, empMachineID, floorDescription, ID, machineID, srp_erp_pay_empattendancereview.floorID, attendanceDate, presentTypeID, DATE_FORMAT(checkIn, '%h:%i %p') checkIn, DATE_FORMAT(checkOut, '%h:%i %p') checkOut, DATE_FORMAT(onDuty, '%h:%i %p') onDuty, DATE_FORMAT(offDuty, '%h:%i %p') offDuty, lateHours, earlyHours, OTHours, mustCheck, normalTime,normalDay, weekend, holiday, NDaysOT, weekendOTHours, holidayOTHours, realTime FROM srp_erp_pay_empattendancereview LEFT JOIN `srp_employeesdetails` ON srp_erp_pay_empattendancereview.empID = srp_employeesdetails.EIdNo LEFT JOIN `srp_erp_pay_floormaster` ON srp_erp_pay_floormaster.floorID = srp_erp_pay_empattendancereview.floorID INNER JOIN `srp_erp_employeemanagers` on srp_erp_employeemanagers.empID=srp_employeesdetails.EIdNo WHERE attendanceDate = '{$attendanceDate}' AND srp_erp_pay_empattendancereview.companyID = {$companyID} AND confirmedYN = 1 AND srp_erp_pay_empattendancereview.floorID=$floorID";
        }

        $qry2 = "SELECT ROUND(noPayAmount, 2) as noPayAmount,ROUND(noPaynonPayrollAmount, 2) as noPaynonPayrollAmount, isWeekEndDay,ROUND(paymentOT, 2) as paymentOT,approvedComment,approvedYN, srp_erp_pay_empattendancereview.empID, ECode, Ename1, Ename2, empMachineID, floorDescription, ID, machineID, srp_erp_pay_empattendancereview.floorID, attendanceDate, presentTypeID, DATE_FORMAT(checkIn, '%h:%i %p') checkIn, DATE_FORMAT(checkOut, '%h:%i %p') checkOut, DATE_FORMAT(onDuty, '%h:%i %p') onDuty, DATE_FORMAT(offDuty, '%h:%i %p') offDuty, lateHours, earlyHours, OTHours,  mustCheck, normalTime,normalDay, weekend, holiday, NDaysOT, weekendOTHours, holidayOTHours, realTime FROM srp_erp_pay_empattendancereview LEFT JOIN `srp_employeesdetails` ON srp_erp_pay_empattendancereview.empID = srp_employeesdetails.EIdNo LEFT JOIN `srp_erp_pay_floormaster` ON srp_erp_pay_floormaster.floorID = srp_erp_pay_empattendancereview.floorID INNER JOIN `srp_erp_employeemanagers` on srp_erp_employeemanagers.empID=srp_employeesdetails.EIdNo WHERE  srp_erp_pay_empattendancereview.companyID = {$companyID} $where AND confirmedYN = 1 AND srp_erp_employeemanagers.level=0 AND srp_erp_employeemanagers.active=1 AND managerID={$managerId}";

        if ($this->input->post('col') == 'employee') {
            $qry = $qry2;
        }

        $data['tempAttData'] = $this->db->query($qry)->result_array();
        $data['hideedit'] = $hideedit;
        $data['attendanceDate'] = $attendanceDate;
        $data['approvedYN'] = $approvedYN;
        $data['datefrom'] = $datefrom;
        $data['dateto'] = $dateto;
        $data['floorID'] = $floorID;
        echo $this->load->view('system/hrm/ajax/attendaceApprovalList', $data, true);
    }

    function approveattendlist()
    {
        $hiddenID = $this->input->post('hiddenID');
        $ID = $this->input->post('ID');


        $masterID = $this->input->post('masterID');

        $presentTypeID = $this->input->post('presentTypeID');
        $comments = $this->input->post('approvedComment');
        $empID = $this->input->post('empID');

        $attendanceDate = $this->input->post('attendanceDate');
        $empName = $this->input->post('empName');
        $leave = $this->input->post('leave');
        $companyID = current_companyID();

        $new_array = array();
        $leave_array = array();
        $getArr = array();
        $x = 0;

        if (!empty($hiddenID)) {
            foreach ($hiddenID as $key => $val) {
                /*   if ($val == 0) {
                       continue;
                   }*/
                if ($val == 1) {


                    if ($presentTypeID[$key] == '') {
                        echo json_encode(array('error' => 'e', 'message' => 'Please Select Present Type for ' . $empName[$key]));
                        exit;
                    }
                    if ($leave[$key] != 0) {
                        //Get last leave no
                        $lastCodeArray = $this->db->query("SELECT serialNo FROM srp_erp_leavemaster WHERE companyID={$companyID}
                                            ORDER BY leaveMasterID DESC LIMIT 1")->row_array();
                        $lastCodeNo = $lastCodeArray['serialNo'];
                        $lastCodeNo = ($lastCodeNo == null) ? 1 : $lastCodeArray['serialNo'] + 1;

                        $this->load->library('sequence');
                        $dCode = $this->sequence->sequence_generator('LA', $lastCodeNo);

                        $leave_array[$key]['empID'] = $empID[$key];
                        $leave_array[$key]['leaveTypeID'] = $leave[$key];
                        $leave_array[$key]['startDate'] = $attendanceDate[$key];
                        $leave_array[$key]['endDate'] = $attendanceDate[$key];;
                        $leave_array[$key]['days'] = 1;
                        $leave_array[$key]['documentCode'] = $dCode;
                        $leave_array[$key]['serialNo'] = $lastCodeNo;
                        $leave_array[$key]['entryDate'] = date('Y-m-d');
                        $leave_array[$key]['comments'] = 'From Attendance';
                        $leave_array[$key]['confirmedYN'] = 1;
                        $leave_array[$key]['confirmedByEmpID'] = current_userID();
                        $leave_array[$key]['confirmedByName'] = current_user();
                        $leave_array[$key]['confirmedDate'] = date('Y-m-d');
                        $leave_array[$key]['approvedYN'] = 1;
                        $leave_array[$key]['approvedDate'] = date('Y-m-d');;
                        $leave_array[$key]['approvedbyEmpID'] = current_userID();
                        $leave_array[$key]['approvedbyEmpName'] = current_user();
                        $leave_array[$key]['companyID'] = $companyID;
                        $leave_array[$key]['companyCode'] = current_companyCode();
                        $leave_array[$key]['createdUserGroup'] = current_user_group();
                        $leave_array[$key]['createdPCID'] = current_pc();
                        $leave_array[$key]['createdUserID'] = current_userID();
                        $leave_array[$key]['createdDateTime'] = current_date(true);
                        $leave_array[$key]['createdUserName'] = current_user();
                        $leave_array[$key]['timestamp'] = current_date(true);
                        $leave_array[$key]['isAttendance'] = 1;
                    }

                    $new_array[$key]['ID'] = $masterID[$key];
                    $new_array[$key]['approvedYN'] = 1;
                    $new_array[$key]['approvedComment'] = $comments[$key];
                    $new_array[$key]['presentTypeID'] = $presentTypeID[$key];
                    $new_array[$key]['approvedBy'] = current_userID();
                    $new_array[$key]['approvedDate'] = date('Y-m-d');

                    if (in_array($presentTypeID[$key], array(1))) {   /*1	=Ontitme, */
                        $getArr[$x] = $empID[$key];

                        $x++;
                    }


                }
            }
        }

        if (!empty($getArr)) {
            /*Hourly Accrual*/
            $commaList = implode(', ', $getArr);
            $qry = "SELECT EIdNo, leaveTypeID, srp_erp_leavegroup.leaveGroupID, noOfHours, attendanceDate, workHours, noOfHourscompleted FROM `srp_employeesdetails` INNER JOIN `srp_erp_leavegroup` ON srp_employeesdetails.leaveGroupID = srp_erp_leavegroup.leaveGroupID AND companyID = {$companyID} AND isMonthly = 2 INNER JOIN `srp_erp_leavegroupdetails` ON srp_erp_leavegroup.leaveGroupID = srp_erp_leavegroupdetails.leaveGroupID INNER JOIN (SELECT empID, attendanceDate, checkIn, checkOut,  (HOUR(`checkOut`) * 60 + MINUTE(`checkOut`)) - (HOUR(`checkIn`) * 60 + MINUTE(`checkIn`)) AS workHours FROM `srp_erp_pay_empattendancereview` WHERE attendanceDate = '{$attendanceDate[0]}' AND companyID = {$companyID}) att ON att.empID = EIdNo WHERE Erp_companyID = {$companyID} AND EIdNo IN($commaList) HAVING workHours >= noOfHourscompleted ";
            $hourlyaccrual = $this->db->query($qry)->result_array();
        }


        $update = false;
        if (!empty($new_array)) {
            $update = $this->db->update_batch('srp_erp_pay_empattendancereview', $new_array, 'ID');
            if (!empty($hourlyaccrual)) {
                $currYear = date('Y');
                $accualmaster = $this->db->query("SELECT * FROM `srp_erp_leaveaccrualmaster` WHERE year = {$currYear} AND isHourly =1")->row_array();

                if (empty($accualmaster)) {
                    /*if accrual master not created*/
                    $this->load->library('sequence');
                    $code = $this->sequence->sequence_generator('LAM');
                    $data['companyID'] = $companyID;
                    $data['leaveaccrualMasterCode'] = $code;
                    $data['documentID'] = 'LAM';
                    $data['description'] = 'Hourly Accrual for -' . $currYear;
                    $data['year'] = $currYear;
                    $data['manualYN'] = 0;
                    $data['createdUserGroup'] = current_user_group();
                    $data['createDate'] = date('Y-m-d H:i:s');
                    $data['createdpc'] = current_pc();
                    $data['confirmedYN'] = 1;
                    $data['confirmedby'] = $this->common_data['current_userID'];
                    $data['confirmedDate'] = current_date();
                    $data['isHourly'] = 1;

                    $this->db->insert('srp_erp_leaveaccrualmaster', $data);
                    $last_id = $this->db->insert_id();
                } else {
                    $last_id = $accualmaster['leaveaccrualMasterID'];
                }
                $y = 0;
                foreach ($hourlyaccrual as $houracc) {


                    $detail[$y]['leaveaccrualMasterID'] = $last_id;
                    $detail[$y]['empID'] = $houracc['EIdNo'];
                    $detail[$y]['leaveGroupID'] = $houracc['leaveGroupID'];
                    $detail[$y]['leaveType'] = $houracc['leaveTypeID'];
                    $detail[$y]['hoursEntitled'] = $houracc['noOfHours'];
                    $detail[$y]['description'] = 'Hourly Accrual ' . $houracc['attendanceDate'];
                    $detail[$y]['createDate'] = $houracc['attendanceDate'];
                    $detail[$y]['createdUserGroup'] = current_user_group();
                    $detail[$y]['createdPCid'] = current_pc();
                    $detail[$y]['initalDate'] = $houracc['attendanceDate'];
                    $detail[$y]['manualYN'] = 0;
                    $y++;

                }
                $this->db->insert_batch('srp_erp_leaveaccrualdetail', $detail);

            }

        }
        if ($update) {
            if (!empty($leave_array)) {
                $this->db->insert_batch('srp_erp_leavemaster', $leave_array);
            }

            echo json_encode(array('error' => 's', 'message' => 'Successfully approved'));
        } else {
            echo json_encode(array('error' => 'e', 'message' => 'Failed'));
        }
    }

    function attendancegetLeave()
    {
        $empID = $this->input->post('empID');
        $attendanceDate = $this->input->post('attendanceDate');
        $companyID = current_companyID();

        $q = "SELECT isAllowminus,srp_erp_leavetype.leaveTypeID,srp_erp_leavetype.description FROM srp_employeesdetails LEFT JOIN `srp_erp_leavegroupdetails` ON srp_employeesdetails.leaveGroupID = srp_erp_leavegroupdetails.leaveGroupID INNER JOIN `srp_erp_leavetype`  on srp_erp_leavetype.leaveTypeID=srp_erp_leavegroupdetails.leaveTypeID WHERE EIdNo = {$empID} AND Erp_companyID={$companyID}";
        $drop = $this->db->query($q)->result_array();
        $select = "<select onchange='getleavebalance(this.value)' id='leaveTypeID' name='leaveTypeID' class='select2'>";
        if ($drop) {
            foreach ($drop as $value) {

                $select .= "<option data-isAllowminus='" . $value['isAllowminus'] . "' value='" . $value['leaveTypeID'] . "'>" . $value['description'] . "</option>";


            }
            $select .= "</select>";

            echo json_encode(array('error' => 0, 'message' => $select));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Leave group not assigned for this employee'));
        }

    }

    function greaterOrEqualZero()
    {

        $empMachineID = $this->input->post('empMachineID');

        if ($empMachineID > 0) {
            return true;
        } else {
            $this->form_validation->set_message('greaterOrEqualZero', 'Machine Id is not valid.');
            return false;
        }

    }

    /*Employee Type*/
    public function fetch_employee_types()
    {
        $this->datatables->select('EmpContractTypeID, Description, IFNULL((SELECT COUNT(EmployeeConType) FROM srp_employeesdetails WHERE EmployeeConType = t1.EmpContractTypeID GROUP BY EmpContractTypeID), 0 ) AS usageCount, employeeType, period, typeID')
            ->from('srp_empcontracttypes AS t1')
            ->join('srp_erp_systememployeetype AS t2', 't1.typeID=t2.employeeTypeID')
            ->add_column('periodStr', '<span class="pull-right">$1</span>', 'period')
            ->add_column('edit', '$1', 'action_employee_type(EmpContractTypeID, usageCount)')
            ->where('Erp_CompanyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function saveEmployeeType()
    {
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('conType', 'Employee Type', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->saveEmployeeType());
        }
    }


    public function deleteEmployeeDetail()
    {
        $this->form_validation->set_rules('hidden-id', 'Employee Detail ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->deleteEmployeeDetail());
        }
    }

    public function editEmployeeDetails()
    {
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('hidden-id', 'Employee Type ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->editEmployeeDetails());
        }
    }

    function leaveTypebyleaveGroup()
    {
        $leavegroupID = $this->input->post('leavegroupID');

        $leaveTypes = $this->db->query("SELECT srp_erp_leavetype.leaveTypeID,srp_erp_leavetype.description FROM srp_erp_leavegroupdetails LEFT JOIN srp_erp_leavetype on srp_erp_leavetype.leaveTypeID=srp_erp_leavegroupdetails.leaveTypeID WHERE leaveGroupID = {$leavegroupID}")->result_array();

        $html = "<select name='leaveType' class='form-control frm_input' id='leaveType'>";
        $html .= "<option value=''>Select a Type</option>";
        if ($leaveTypes) {
            foreach ($leaveTypes as $leave) {
                $html .= '<option value="' . $leave['leaveTypeID'] . '">' . $leave['description'] . '</option>';
            }
        }
        $html .= '</select>';

        echo $html;

    }

    function delete_leaveGroup()
    {
        $id = $this->input->post('leaveGroupID');
        $companyID = current_companyID();
        $this->db->delete('srp_erp_leavegroup', array('companyID' => $companyID, 'leaveGroupID' => $id));
        $this->db->delete('srp_erp_leavegroupdetails', array('leaveGroupID' => $id));
        echo json_encode(TRUE);
    }

    function deleteLeavedeltails()
    {
        $id = $this->input->post('leaveGroupDetailID');
        $companyID = current_companyID();

        $this->db->delete('srp_erp_leavegroupdetails', array('leaveGroupDetailID' => $id));
        echo json_encode(TRUE);
    }

    public function discharge_update()
    {
        $this->form_validation->set_rules('updateID', 'Employee Detail ID', 'required');
        $this->form_validation->set_rules('isDischarged', 'Is Discharged', 'required');
        $this->form_validation->set_rules('dischargedDate', 'Discharged Date', 'required');
        $this->form_validation->set_rules('lastWorkingDate', 'Last Working Date', 'required');

        $updateID = $this->input->post('updateID');
        $isDischarged = $this->db->query("SELECT isDischarged FROM srp_employeesdetails WHERE EIdNo = '$updateID'")->row('isDischarged');
        if ($isDischarged) {
            exit(json_encode(array('e', "<p>Employee is Discharged. You cannot edit.</p>")));
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->discharge_update());
        }
    }

    public function save_leave_annualAccrual()
    {
        $this->Employee_model->save_leave_annualAccrual();
    }

    public function update_leave_annualAccrual(){
        $description = $this->input->post('description');
        $masterID = $this->input->post('masterID');
        $companyID = current_companyID();

        $leaveData = $this->db->query("SELECT confirmedYN, leaveMasterID FROM srp_erp_leaveaccrualmaster
                                       WHERE leaveaccrualMasterID={$masterID} AND companyID={$companyID}")->row_array();

        if($leaveData['confirmedYN'] == 1){
           die( json_encode(['e', 'This document is already confirmed']) );
        }

        if(!empty($leaveData['leaveMasterID'])){
            die( json_encode(['e', 'This document is generated from holiday calendar,<br/>You can not update this document.']) );
        }

        $updateData = [
            'leaveaccrualMasterID' => $masterID,
            'companyID' => $companyID
        ];

        $this->db->trans_start();

        $this->db->where($updateData)->update('srp_erp_leaveaccrualmaster', ['description' => $description]);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            echo json_encode(['s', 'Updated successfully.']);
        }else{
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in process.']);
        }

    }

    function delete_accrual(){
        $companyID = current_companyID();
        $masterID = $this->input->post('id');

        $leaveData = $this->db->query("SELECT confirmedYN, leaveMasterID FROM srp_erp_leaveaccrualmaster
                                       WHERE leaveaccrualMasterID={$masterID} AND companyID={$companyID}")->row_array();

        if($leaveData['confirmedYN'] == 1){
            die( json_encode(['e', 'This document is already confirmed.']) );
        }

        if(!empty($leaveData['leaveMasterID'])){
            die( json_encode(['e', 'This document is generated from holiday calendar,<br/>You can not delete this document.']) );
        }

        $where = [
            'leaveaccrualMasterID' => $masterID,
            'companyID' => $companyID
        ];

        $this->db->trans_start();

        $this->db->where($where)->delete('srp_erp_leaveaccrualmaster');
        $this->db->where(['leaveaccrualMasterID' => $masterID])->delete('srp_erp_leaveaccrualdetail');

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            echo json_encode(['s', 'Deleted successfully.']);
        }else{
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in process.']);
        }

    }

    public function leaveannualaccrualMaster()
    {
        $this->datatables->select("leaveaccrualMasterID,leaveaccrualMasterCode,srp_erp_leaveaccrualmaster.description,concat(year,' - ',MONTHNAME(STR_TO_DATE(month, '%m'))) as month,srp_erp_leavegroup.Description as leaveGroup, confirmedYN", false)
            ->from("srp_erp_leaveaccrualmaster")
            ->join("srp_erp_leavegroup", "srp_erp_leaveaccrualmaster.leaveGroupID = srp_erp_leavegroup.leaveGroupID", "left")
            ->where("manualYN", 0)
            ->where("srp_erp_leaveaccrualmaster.policyMasterID", 1)
            ->where("srp_erp_leaveaccrualmaster.companyID", current_companyID())
            ->add_column("edit", "$1", "AnnualaccrualAction(leaveaccrualMasterID, confirmedYN)");
        echo $this->datatables->generate();


    }

    function generateCalender()
    {

        $companyID = current_companyID();
        $year = $this->input->post('year');
        $existYear = $this->db->query("select GROUP_CONCAT(year) as year,MAX(year)+1 as nextyear,MIN(year) as minYear from (SELECT year FROM `srp_erp_calender` WHERE `companyID` = $companyID GROUP BY year )t")->row_array();

        if (!empty($existYear) && $existYear['nextyear'] != '') {
            $prevYear = $year - 1;
            if ($existYear['nextyear'] != $year) {
                exit(json_encode(array('error' => 1, 'message' => "You can only able to create calender for  " . $existYear['nextyear'] . " ")));
            }

        }
        $nextyear = $year + 1;
        $startDay = $year . '-01-01';
        $endDay = $nextyear . '-01-01';

        $Sunday = $Monday = $Tuesday = $Wednesday = $Thursday = $Friday = $Saturday = 0;
        $daysWeek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        foreach ($daysWeek as $day) {
            ${"$day"} = ($this->input->post($day) != '' ? $this->input->post($day) : 0);
        }

        // Do not touch variable defined dynamically - shahmee
        $this->db->query("call generateCalender('{$startDay}','{$endDay}',{$companyID},$Sunday,$Monday,$Tuesday,$Wednesday,$Thursday,$Friday,$Saturday)");

        exit(json_encode(array('error' => 0, 'message' => "Successfully calender created for - {$year} ")));

    }

    function refresh_policy()
    {
        $masterID = $this->input->post('masterID');
        $result = $this->db->query("select isMonthly from `srp_erp_leavegroup` WHERE leaveGroupID={$masterID} ")->row_array();
        echo form_dropdown('leaveTypeID', leavemaster_dropdown($result['isMonthly']), '', 'class="form-control select2" id="leaveTypeID" required');

    }

    function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        $array = array();
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        return $array;
    }

    function employee_leave_page()
    {
        $empID = $this->input->post('empID');
        $policyMasterID = $this->input->post('policyMasterID');
        $leaveGroupID = $this->input->post('leaveGroupID');
        $applicationType = $this->input->post('applicationType');
        $data['applicationType'] = $applicationType;
        $data['policyMasterID'] = $policyMasterID;
        $data['leaveGroupID'] = $leaveGroupID;
        $data['empID'] = $empID;
        $data['showYN'] = $this->input->post('showYN');
        $data['leaveTypeID'] = $this->input->post('leaveTypeID');


        if ($policyMasterID == 2) {
            echo $this->load->view('system/hrm/ajax/employee_leave_page_hour_form', $data, true);
        } else {
            echo $this->load->view('system/hrm/ajax/employee_leave_page_form', $data, true);
        }


    }

    function leaveEmployeeCalculation()
    {
        $policyMasterID = $this->input->post('policyMasterID');
        $companyID = current_companyID();
        $leaveTypeID = $this->input->post('leaveTypeID');
        $halfDay = $this->input->post('halfDay');
        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');
        $isAllowminus = $this->input->post('isAllowminus');
        $isCalenderDays = $this->input->post('isCalenderDays');
        $isCalenderDays = ($isCalenderDays == '' ? 0 : $isCalenderDays);
        $entitleSpan = $this->input->post('entitleSpan');
        $entitleSpan = ($entitleSpan == '' ? 0 : $entitleSpan);
        /*date diff*/

        if ($policyMasterID != 2) {

            $date1 = new DateTime("$startDate");
            $date2 = new DateTime("$endDate");
            $diff = $date2->diff($date1)->format("%a");
            $dateDiff = $diff + 1;
            $dateDiff2 = $diff + 1;
            $calenderDays['workingDays'] = 0;
            $datetime1 = date('Y-m-d', strtotime($startDate));
            $datetime2 = date('Y-m-d', strtotime($endDate));
            if ($datetime1 > $datetime2) {
                echo json_encode(array('error' => 1, 'message' => 'Please check start and end date.'));
                exit;
            }

            if ($isCalenderDays != 1) {
                $sd = explode('-', $startDate);
                $sYear = $sd[0];
                $sMonth = $sd[1];

                $ed = explode('-', $endDate);
                $eYear = $ed[0];
                $eMonth = $ed[1];

                $calendervalidate = $this->db->query("SELECT sum(IF(monthnumber = {$sMonth} && year={$sYear}, 1, 0)) as startDate ,  sum(IF(monthnumber = {$eMonth} && year={$eYear}, 1, 0)) as endDate FROM `srp_erp_calender` WHERE monthnumber AND year AND companyID={$companyID}")->row_array();

                if ($calendervalidate['startDate'] == 0 || $calendervalidate['endDate'] == 0) {
                    echo json_encode(array('error' => 1, 'message' => 'Calender not configured for selected date.'));
                    exit;
                }

                $calenderDays = $this->db->query("SELECT SUM(IF(fulldate != '', 1, 0)) AS nonworkingDays, SUM(IF(fulldate != '', 1, 0)) - SUM(IF(weekend_flag = 1 || holiday_flag = 1, 1, 0)) AS workingDays FROM `srp_erp_calender` WHERE fulldate BETWEEN '{$startDate}' AND '{$endDate}' AND companyID = {$companyID}")->row_array();
                if ($calenderDays['workingDays'] != null) {
                    /*    $calenderDays['workingDays']=  ($calenderDays['workingDays'] == null ? 0:$calenderDays['workingDays']);*/
                    /* if ($calenderDays['workingDays'] == null) {
                         echo json_encode(array('error' => 1, 'message' => 'Calender is not set for this company'));
                         exit;
                     }
                     }*/
                    $dateDiff = $calenderDays['workingDays'];
                }
            }
            if ($halfDay == 1) {
                $dateDiff = $dateDiff2 = $calenderDays['workingDays'] = 0.5; /*half day*/
            }
            $leaveBlance = $entitleSpan - $dateDiff;
            if ($isAllowminus != 1) {
                if ($leaveBlance < 0) {
                    echo json_encode(array('error' => 3, 'message' => 'The maximum leave accumulation is  ' . "$entitleSpan" . ' days'));
                    exit;
                }
            }

            echo json_encode(array('error' => 0, 'appliedLeave' => $dateDiff2, 'leaveBlance' => $leaveBlance, 'calenderYN' => $isCalenderDays, 'workingDays' => $calenderDays['workingDays']));
            exit;
        } else {
            $datetime1 = date('Y-m-d H:i:s', strtotime($startDate));
            $datetime2 = date('Y-m-d H:i:s', strtotime($endDate));
            if ($datetime1 < $datetime2) {
                $dteStart = new DateTime($startDate);
                $dteEnd = new DateTime($endDate);

                $dteDiff = $dteStart->diff($dteEnd);
                $hour = $dteDiff->format("%H");
                $minutes = $dteDiff->format('%I');
                $day = $dteDiff->format('%d');
                $totalMinutes = ($day * 1440) + ($hour * 60) + $minutes;
            } else {
                echo json_encode(array('error' => 1, 'message' => 'Please check the start date and end date'));
                exit;
            }
            $balance = $entitleSpan - $totalMinutes;

            if ($isAllowminus != 1) {
                if ($balance < 0) {

                    $hours = floor($entitleSpan / 60);
                    $min = $entitleSpan - ($hours * 60);

                    $entitle = $hours . "h:" . $min . "m";
                    echo json_encode(array('error' => 3, 'message' => 'The maximum leave accumulation is  ' . "$entitle" . ' '));
                    exit;
                }
            }

            echo json_encode(array('error' => 0, 'appliedLeave' => $totalMinutes, 'leaveBlance' => $balance));

        }


    }


    public function save_employeesLeave()
    {

        $this->form_validation->set_rules('empName', 'Employee ID', 'trim|required|numeric');
        $this->form_validation->set_rules('leaveTypeID', 'Leave Type', 'trim|required|numeric');
        $this->form_validation->set_rules('startDate', 'Start Date', 'trim|required');
        $this->form_validation->set_rules('endDate', 'End Date', 'trim|required');
        // $this->form_validation->set_rules('entitleSpan', 'Leave Entitled', 'trim|required|numeric');
        /* $this->form_validation->set_rules('appliedLeave', 'Leave Applied', 'trim|required|numeric');
         $this->form_validation->set_rules('leaveBlance', 'Leave Balance', 'trim|required|numeric');*/

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $empID = $this->input->post('empName');
            $leaveTypeID = $this->input->post('leaveTypeID');
            $applicationType = $this->input->post('applicationType');
            $canApplyMultiple = getPolicyValues('LP', 'All');

            /*leave adjustment status for last leave group change of this employee */
            $adjustmentStatus = $this->db->query("SELECT adjustmentDone FROM srp_erp_leavegroupchangehistory WHERE empID={$empID} ORDER BY id DESC LIMIT 1")->row('adjustmentDone');
            if($adjustmentStatus == 0){
                die(json_encode(['e', 'Leave adjustment process was not processed for previous leave group change.<br/>
                                       Please process the leave adjustment and try again.']));
            }

            if ($applicationType == 1 AND $canApplyMultiple == 0) {
                $leaveExist = $this->db->query("select * from srp_erp_leavemaster WHERE (approvedYN is null OR approvedYN=0)
                                                AND empID={$empID} AND applicationType=1 AND leaveTypeID={$leaveTypeID}")->row_array();
                if (!empty($leaveExist)) {
                    die( json_encode(array('e', 'Employee has pending leave application in process.')) );
                }
            }

            if ($applicationType == 2) {
                $isPlanApplicable = $this->db->query("SELECT isPlanApplicable FROM srp_erp_leavetype
                                                  WHERE leaveTypeID={$leaveTypeID}")->row('isPlanApplicable');

                if ($isPlanApplicable != 1) {
                    die(json_encode(array('e', 'This leave type is not applicable for leave plan')));
                }
            }

            if ($this->input->post('appliedLeave') >= 0 && $this->input->post('appliedLeave') != '') {
                /* echo json_encode($this->Employee_model->update_employeesLeave());*/
            } else {
                die( json_encode(array('e', 'Please check the start date and end date')) );
            }

            /***Validate is there is a leave falling in this date range ***/
            $companyID = current_companyID();
            $startDate = $this->input->post('startDate');
            $endDate = $this->input->post('endDate');
            $leaveApp = $this->db->query("SELECT leaveMasterID, documentCode, IF(applicationType=1, 'leave application', 'leave plan') AS appType
                                          FROM srp_erp_leavemaster
                                          WHERE companyID={$companyID} AND empID={$empID} AND (cancelledYN = 0 OR cancelledYN is null)
                                          AND (
                                              ( ( '{$startDate}' BETWEEN startDate AND endDate ) OR ( '{$endDate}' BETWEEN startDate AND endDate ) )
                                              OR
                                              ( ( startDate BETWEEN '{$startDate}' AND '{$endDate}' ) OR ( endDate BETWEEN '{$startDate}' AND '{$endDate}' ) )
                                          )")->row_array();

            if(!empty($leaveApp)){
                die( json_encode(array('e', 'There is a '.$leaveApp['appType'].' ['.$leaveApp['documentCode'].'] already exist in this date range ')) );
            }

            //Validate this employee assigned for a leave covering on this leave date
            $coveringValidated = $this->input->post('coveringValidated');
            $leaveCovering = $this->db->query("SELECT leaveMasterID, documentCode, IF(applicationType=1, 'leave application', 'leave plan') AS appType
                              FROM srp_erp_leavemaster
                              WHERE companyID={$companyID} AND coveringEmpID={$empID} AND (cancelledYN=0 OR cancelledYN IS NULL )
                              AND (
                                  ( ( '{$startDate}' BETWEEN startDate AND endDate ) OR ( '{$endDate}' BETWEEN startDate AND endDate ) )
                                    OR
                                  ( ( startDate BETWEEN '{$startDate}' AND '{$endDate}' ) OR ( endDate BETWEEN '{$startDate}' AND '{$endDate}' ) )
                              )")->row_array();

            if(!empty($leaveCovering) && $coveringValidated == 0 ){
                $msg = 'You have assigned as covering employee for leave application ['.$leaveCovering['documentCode'].']';
                $resData = [
                    'covering' => '1',
                    'requestType' => 'save',
                    'isConfirmed' => $this->input->post('isConfirmed')
                ];
                die( json_encode(array('w', $msg, $resData)) );
            }

            $coveringEmpID = $this->input->post('coveringEmpID');

            if($this->input->post('isConfirmed') == 1){
                //Check covering employee is in approval setup
                $isCovering = $this->db->query("SELECT approvalSetupID FROM srp_erp_leaveapprovalsetup WHERE companyID={$companyID} AND approvalType=4")->row('approvalSetupID');

                if(!empty($isCovering) && empty($coveringEmpID)){
                    die( json_encode(array('e', 'Covering employee is required')) );
                }
            }

            $coveringAvailabilityValidated = $this->input->post('coveringAvailabilityValidated');

            //Validate covering employee leave get clash with this leave date
            if(!empty($coveringEmpID) && $coveringAvailabilityValidated == 0 ){
                $leaveCoveringAvailability = $this->db->query("SELECT leaveMasterID, documentCode, IF(applicationType=1, 'leave application', 'leave plan') AS appType
                              FROM srp_erp_leavemaster
                              WHERE companyID={$companyID} AND empID={$coveringEmpID} AND (cancelledYN=0 OR cancelledYN IS NULL )
                              AND (
                                  ( ( '{$startDate}' BETWEEN startDate AND endDate ) OR ( '{$endDate}' BETWEEN startDate AND endDate ) )
                                    OR
                                  ( ( startDate BETWEEN '{$startDate}' AND '{$endDate}' ) OR ( endDate BETWEEN '{$startDate}' AND '{$endDate}' ) )
                              )")->row_array();

                if(!empty($leaveCoveringAvailability)){
                    $msg = 'Covering employee not available in this date range.<br/>'.$leaveCoveringAvailability['appType'].' ['.$leaveCoveringAvailability['documentCode'].'] ';
                    $resData = [
                        'covering' => '2',
                        'requestType' => 'save',
                        'isConfirmed' => $this->input->post('isConfirmed')
                    ];
                    die( json_encode(array('w', $msg, $resData)) );
                }
            }
            /* $endDate = $this->input->post('endDate');
             $isPayrollProcessed = isPayrollProcessed($endDate);*/
            echo json_encode($this->Employee_model->save_employeesLeave());
            exit;
            /*     if ($isPayrollProcessed['status'] == 'N') {


                 } else {
                     $greaterThanDate = date('Y - F', strtotime($isPayrollProcessed['year'] . '-' . $isPayrollProcessed['month'] . '-01'));
                     echo json_encode(array('e', 'Leave date should be  <p> greater than [ ' . $greaterThanDate . ' ] '));
                 }
                 exit;*/
        }
    }


    public function update_employeesLeave()
    {
        $this->form_validation->set_rules('leaveMasterID', 'leave Master ID', 'trim|required|numeric');
        $this->form_validation->set_rules('leaveTypeID', 'Leave Type', 'trim|required|numeric');
        $this->form_validation->set_rules('startDate', 'Start Date', 'trim|required|date');
        $this->form_validation->set_rules('endDate', 'End Date', 'trim|required|date');
        $this->form_validation->set_rules('empName', 'Employee ID', 'trim|required|numeric');
        /* $this->form_validation->set_rules('appliedLeave', 'Leave Applied', 'trim|required|numeric');
         $this->form_validation->set_rules('leaveBlance', 'Balance', 'trim|required|numeric');*/


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $empID = $this->input->post('empName');
            $leaveMasterID = $this->input->post('leaveMasterID');

            /*leave adjustment status for last leave group change of this employee */
            $adjustmentStatus = $this->db->query("SELECT adjustmentDone FROM srp_erp_leavegroupchangehistory WHERE empID={$empID} ORDER BY id DESC LIMIT 1")->row('adjustmentDone');
            if($adjustmentStatus == 0){
                die(json_encode(['e', 'Leave adjustment process was not processed for previous leave group change.<br/>
                                       Please process the leave adjustment and try again.']));
            }

            $leaveDet = $this->Employee_model->employeeLeave_details($leaveMasterID);



            if($leaveDet['approvedYN'] == 1){
                die( json_encode(['e', 'This document already approved. You can not make changes on this.']) );
            }

            if($leaveDet['confirmedYN'] == 1){
                die( json_encode(['e', 'This document already confirmed. You can not make changes on this.']) );
            }

            $leaveTypeID = $this->input->post('leaveTypeID');
            $applicationType = $this->input->post('applicationType');
            $canApplyMultiple = getPolicyValues('LP', 'All');

            if ($applicationType == 1 AND $canApplyMultiple == 0) {
                $leaveExist = $this->db->query("select leaveMasterID from srp_erp_leavemaster WHERE (approvedYN is null OR approvedYN=0)
                                                AND empID={$empID} AND applicationType=1 AND leaveTypeID={$leaveTypeID}")->row('leaveMasterID');
                if (!empty($leaveExist) && $leaveExist != $leaveMasterID) {
                    echo json_encode(array('e', 'Employee has pending leave application in process.'));
                    exit;
                }
            }

            if ($applicationType == 2) {
                $isPlanApplicable = $this->db->query("SELECT isPlanApplicable FROM srp_erp_leavetype
                                                  WHERE leaveTypeID={$leaveTypeID}")->row('isPlanApplicable');

                if($isPlanApplicable != 1){
                    die( json_encode(array('e', 'This leave type is not applicable for leave plan')) );
                }
            }

            /***Validate is there is a leave falling in this date range ***/
            $companyID = current_companyID();
            $startDate = $this->input->post('startDate');
            $endDate = $this->input->post('endDate');
            $leaveApp = $this->db->query("SELECT leaveMasterID, documentCode, IF(applicationType=1, 'leave application', 'leave plan') AS appType
                                          FROM srp_erp_leavemaster
                                          WHERE companyID={$companyID} AND empID={$empID} AND (cancelledYN=0 OR cancelledYN IS NULL )
                                          AND (
                                              ( ( '{$startDate}' BETWEEN startDate AND endDate ) OR ( '{$endDate}' BETWEEN startDate AND endDate ) )
                                                OR
                                              ( ( startDate BETWEEN '{$startDate}' AND '{$endDate}' ) OR ( endDate BETWEEN '{$startDate}' AND '{$endDate}' ) )
                                          )")->row_array();

            if(!empty($leaveApp) && $leaveApp['leaveMasterID'] != $leaveMasterID){
                die( json_encode(array('e', 'There is a '.$leaveApp['appType'].' ['.$leaveApp['documentCode'].'] already exist in this date range ')) );
            }

            //Validate this employee assigned for a leave covering on this leave date
            $coveringValidated = $this->input->post('coveringValidated');
            $leaveCovering = $this->db->query("SELECT leaveMasterID, documentCode, IF(applicationType=1, 'leave application', 'leave plan') AS appType
                                          FROM srp_erp_leavemaster
                                          WHERE companyID={$companyID} AND coveringEmpID={$empID} AND (cancelledYN=0 OR cancelledYN IS NULL )
                                          AND (
                                              ( ( '{$startDate}' BETWEEN startDate AND endDate ) OR ( '{$endDate}' BETWEEN startDate AND endDate ) )
                                                OR
                                              ( ( startDate BETWEEN '{$startDate}' AND '{$endDate}' ) OR ( endDate BETWEEN '{$startDate}' AND '{$endDate}' ) )
                                          )")->row_array();

            if(!empty($leaveCovering) && $coveringValidated == 0 ){
                $msg = 'You have assigned as covering employee for leave application ['.$leaveCovering['documentCode'].']';
                $resData = [
                    'covering' => '1',
                    'requestType' => 'update',
                    'isConfirmed' => $this->input->post('isConfirmed')
                ];
                die( json_encode(array('w', $msg, $resData)) );
            }


            $coveringEmpID = $this->input->post('coveringEmpID');

            if($this->input->post('isConfirmed') == 1){
                //Check covering employee is in approval setup
                $isCovering = $this->db->query("SELECT approvalSetupID FROM srp_erp_leaveapprovalsetup WHERE companyID={$companyID} AND approvalType=4")->row('approvalSetupID');

                if(!empty($isCovering) && empty($coveringEmpID)){
                    die( json_encode(array('e', 'Covering employee is required')) );
                }
            }



            $coveringAvailabilityValidated = $this->input->post('coveringAvailabilityValidated');

            //Validate covering employee leave get clash with this leave date
            if(!empty($coveringEmpID) && $coveringAvailabilityValidated == 0 ){
                $leaveCoveringAvailability = $this->db->query("SELECT leaveMasterID, documentCode, IF(applicationType=1, 'leave application', 'leave plan') AS appType
                                          FROM srp_erp_leavemaster
                                          WHERE companyID={$companyID} AND empID={$coveringEmpID} AND (cancelledYN=0 OR cancelledYN IS NULL )
                                          AND (
                                              ( ( '{$startDate}' BETWEEN startDate AND endDate ) OR ( '{$endDate}' BETWEEN startDate AND endDate ) )
                                                OR
                                              ( ( startDate BETWEEN '{$startDate}' AND '{$endDate}' ) OR ( endDate BETWEEN '{$startDate}' AND '{$endDate}' ) )
                                          )")->row_array();

                if(!empty($leaveCoveringAvailability)){
                    $msg = 'Covering employee not available in this date range.<br/>'.$leaveCoveringAvailability['appType'].' ['.$leaveCoveringAvailability['documentCode'].'] ';
                    $resData = [
                        'covering' => '2',
                        'requestType' => 'update',
                        'isConfirmed' => $this->input->post('isConfirmed')
                    ];
                    die( json_encode(array('w', $msg, $resData)) );
                }
            }


            if ($this->input->post('appliedLeave') >= 0 && $this->input->post('appliedLeave') != '') {
                echo json_encode($this->Employee_model->update_employeesLeave());
            } else {
                echo json_encode(array('e', 'Please check the start date and end date'));
            }

        }
    }


    function getEmployeesDataTableShift()
    {
        $companyID = current_companyID();
        /*$epfMasterID = $this->input->post('epfMasterID');
        $data = $this->Report_model->epf_reportData($epfMasterID);
        $payrollID = $data['payrollMasterID'];*/


        /*echo '<pre>'; print_r($data); echo '</pre>';
        echo '<pre>'; print_r($whereNotIn_str); echo '</pre>';*/

        $con = "IFNULL(Ename2, '')";
        $str_lastOCGrade = '(SELECT ocGrade FROM srp_erp_sso_epfreportdetails WHERE empID = EIdNo AND companyID=' . $companyID . ' ORDER BY id DESC LIMIT 1)';


        $this->datatables->select('EIdNo, ECode, CONCAT(' . $con . ') AS empName, DesDescription,
                                    IF(' . $str_lastOCGrade . ' IS NULL, \'\', ' . $str_lastOCGrade . ') AS last_ocGrade');
        $this->datatables->from('srp_employeesdetails');
        $this->datatables->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID');
        $this->datatables->add_column('addBtn', '$1', 'addBtn()');
        $this->datatables->where('srp_employeesdetails.Erp_companyID', $companyID);
        $this->datatables->where('srp_employeesdetails.isPayrollEmployee', 1);
        $this->datatables->where('srp_employeesdetails.isDischarged', 0);

        echo $this->datatables->generate();
    }

    function add_employees_to_shift()
    {
        echo json_encode($this->Employee_model->add_employees_to_shift());
    }

    function export_excel()
    {

        $this->load->library('excel');
        //set cell A1 content with some text
        // $this->excel->getActiveSheet()->setCellValueByColumnAndRow('A1',1,'test');
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Users list');
        // load database
        $this->load->database();
        // load model
        // get all users in array formate
        $data = $this->fetch_employees_for_excel();
        $header = $data['header'];
        $employees = $data['employees'];
        // Header
        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Calibri'
            )
        );

        $styleArray2 = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Calibri'
            )
        );
        $this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $this->excel->getActiveSheet()->fromArray(['Employee list'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray2);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        // Data
        $this->excel->getActiveSheet()->fromArray($employees, null, 'A5');
        //set aligment to center for that merged cell (A1 to D1)
        ob_clean();
        ob_start(); # added
        $filename = 'Employee Details.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        ob_clean(); # remove this
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }

    function fetch_employees_for_excel()
    {
        $columns = $this->input->post('columns');
        $employee_filter = '';
        $segment_filter = '';
        $employee = $this->input->post('employeeCode');
        $segment = $this->input->post('segment');
        $isDischarged = $this->input->post('isDischarged');
        if (!empty($employee)) {
            //$employee = array($this->input->post('employeeCode'));
            $whereIN = "( " . join(" , ", $employee) . " )";
            $employee_filter = " AND EIdNo IN " . $whereIN;
        }
        if (!empty($segment)) {
            $whereIN = "( " . join("' , '", $segment) . " )";
            $segment_filter = " AND t1.segmentID IN " . $whereIN;
        }

        switch ($isDischarged) {
            case 'N':
                $discharged_filter = ' AND isDischarged != 1';
                break;

            case 'Y':
                $discharged_filter = ' AND isDischarged = 1';
                break;

            default:
                $discharged_filter = '';
        }

        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $where = "isSystemAdmin !=1 AND t1.Erp_companyID = " . $companyID . $employee_filter . $segment_filter . $discharged_filter . "";

        $selectStr = "";
        $excelHeader = array();
        foreach ($columns AS $key => $row) {
            $row_arr = explode('|', $row);
            $rowColumn = $row_arr[0];
            array_push($excelHeader, $row_arr[1]);

            switch ($rowColumn) {
                case 'EDOJ':
                    $selectStr .= ', DATE_FORMAT(EDOJ,\'' . $convertFormat . '\') AS doj';
                    break;
                case 'EDOB':
                    $selectStr .= ', DATE_FORMAT(EDOB,\'' . $convertFormat . '\') AS dob';
                    break;
                case 'contractStartDate':
                    $selectStr .= ', DATE_FORMAT(contractStartDate,\'' . $convertFormat . '\') AS contractStart';
                    break;
                case 'contractEndDate':
                    $selectStr .= ', DATE_FORMAT(contractEndDate,\'' . $convertFormat . '\') AS contractEnd';
                    break;
                case 'empConfirmedYN':
                    $selectStr .= ', IF(empConfirmedYN=1,\'Yes\',\'No\') AS  empConfirmedYNStatus';
                    break;
                case 'Gender':
                    $selectStr .= ', IF(Gender=1,\'Male\', \'Female\') AS genderStr';
                    break;
                case 'isDischarged':
                    $selectStr .= ', IF(isDischarged=1, \'Discharged\', \'Active\') AS empStatus';
                    break;
                case 'empMaritialStatus':
                    $selectStr .= ', maritialStatus.description AS empMaritialStatus';
                    break;

                case 'segment':
                    $selectStr .= ', srp_erp_segment.description AS segment';
                    break;

                default :
                    $selectStr .= ',' . $rowColumn;
            }
        }


        $this->db->select($selectStr)
            ->from('srp_employeesdetails AS t1')
            ->join('srp_designation', 'DesignationID=t1.EmpDesignationId', 'LEFT')
            ->join('srp_titlemaster', 'TitleID=EmpTitleId')
            ->join('srp_erp_segment', 'srp_erp_segment.segmentID=t1.segmentID', 'LEFT')
            ->join('srp_countrymaster', 'srp_countrymaster.countryID=t1.EpAddress4', 'LEFT')
            ->join('srp_nationality', 'srp_nationality.NId=t1.Nid', 'LEFT')
            ->join('srp_erp_bloodgrouptype', 'srp_erp_bloodgrouptype.BloodTypeID=t1.BloodGroup', 'LEFT')
            ->join('srp_erp_maritialstatus AS maritialStatus', 'maritialStatus.maritialstatusID=t1.MaritialStatus', 'LEFT')
            ->join('srp_erp_systememployeetype AS employeeType', 'employeeType.employeeTypeID=t1.EmployeeConType', 'LEFT')
            ->join('(SELECT empID, EmpSecondaryCode AS managerCode FROM  srp_erp_employeemanagers
                    JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo=srp_erp_employeemanagers.managerID
                    WHERE companyID=' . $companyID . ' AND active=1) AS repotingManagerTB', 'repotingManagerTB.empID=t1.EIdNo', 'LEFT')
            ->join('(SELECT empID, socialInsuranceNumber FROM srp_erp_socialinsurancedetails WHERE companyID=' . $companyID . ' GROUP BY empID)
                     AS ssoTB', 'ssoTB.empID=t1.EIdNo', 'LEFT')
            ->where($where);
        $rows = $this->db->get()->result_array();

        return ['header' => $excelHeader, 'employees' => $rows];

    }

    function load_nonPayrollEmployees()
    {
        $companyID = current_companyID();
        $result = $this->db->query("SELECT EIdNo, Ename2 as empName, ECode, salaryAcc.*
                                    FROM srp_employeesdetails AS empTB
                                    LEFT JOIN (
                                        SELECT id AS accountID, employeeNo, salaryAcc.bankID, salaryAcc.branchID, accountNo,
                                        accountHolderName, toBankPercentage, isActive, bankName, branchName
                                        FROM srp_erp_non_pay_salaryaccounts AS salaryAcc
                                        JOIN srp_erp_pay_bankmaster AS bankTB ON bankTB.bankID = salaryAcc.bankID AND bankTB.companyID={$companyID}
                                        JOIN srp_erp_pay_bankbranches AS branchesTB ON branchesTB.branchID = salaryAcc.branchID AND branchesTB.companyID={$companyID}
                                        WHERE salaryAcc.companyID={$companyID}
                                    ) AS salaryAcc ON salaryAcc.employeeNo = empTB.EIdNo
                                    WHERE Erp_companyID={$companyID} ORDER BY EIdNo DESC")->result_array();
        $data['accountData'] = $result; //array();

        $this->load->view('system/hrm/ajax/load_empNonPayrollBanksView', $data);
    }

    function save_nonPayBankAccount()
    {
        $this->form_validation->set_rules('empID', 'Account Holder Name', 'trim|required');
        $this->form_validation->set_rules('accHolder', 'Account Holder Name', 'trim|required');
        $this->form_validation->set_rules('accountNo', 'Bank Account Number', 'trim|required');
        $this->form_validation->set_rules('bank_id', 'Bank', 'trim|required');
        $this->form_validation->set_rules('branch_id', 'Branch', 'trim|required');
        $this->form_validation->set_rules('salPerc', 'Salary Transfer %', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {

            $empID = $this->input->post('empID');
            $accHolder = $this->input->post('accHolder');
            $accountNo = $this->input->post('accountNo');
            $bank_id = $this->input->post('bank_id');
            $br_name = $this->input->post('branch_id');
            $salPerc = $this->input->post('salPerc');


            $data = array(
                'employeeNo' => $empID,
                'bankID' => $bank_id,
                'isActive' => 1,
                'accountNo' => $accountNo,
                'accountHolderName' => $accHolder,
                'branchID' => $br_name,
                'toBankPercentage' => $salPerc,
                'companyID' => current_companyID(),
                'companyCode' => current_companyCode(),
                'createdPCID' => current_pc(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdUserGroup' => current_user_group(),
                'createdDateTime' => current_date()
            );

            $this->db->trans_start();

            $this->db->insert('srp_erp_non_pay_salaryaccounts', $data);

            $this->db->trans_complete();

            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                echo json_encode(['s', 'Record inserted successfully']);
            } else {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in process']);
            }

        }
    }

    function update_nonPayBankAccount()
    {
        $this->form_validation->set_rules('empID', 'Account Holder Name', 'trim|required');
        $this->form_validation->set_rules('accountID', 'Update ID', 'trim|required');
        $this->form_validation->set_rules('accHolder', 'Account Holder Name', 'trim|required');
        $this->form_validation->set_rules('accountNo', 'Bank Account Number', 'trim|required');
        $this->form_validation->set_rules('bank_id', 'Bank', 'trim|required');
        $this->form_validation->set_rules('branch_id', 'Branch', 'trim|required');
        $this->form_validation->set_rules('salPerc', 'Salary Transfer %', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {

            $empID = $this->input->post('empID');
            $accountID = $this->input->post('accountID');
            $accHolder = $this->input->post('accHolder');
            $accountNo = $this->input->post('accountNo');
            $bank_id = $this->input->post('bank_id');
            $br_name = $this->input->post('branch_id');
            $salPerc = $this->input->post('salPerc');
            $accStatus = $this->input->post('accStatus');


            $data = array(
                'bankID' => $bank_id,
                'isActive' => $accStatus,
                'accountNo' => $accountNo,
                'accountHolderName' => $accHolder,
                'branchID' => $br_name,
                'toBankPercentage' => $salPerc,
                'companyID' => current_companyID(),
                'companyCode' => current_companyCode(),
                'createdPCID' => current_pc(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdUserGroup' => current_user_group(),
                'createdDateTime' => current_date()
            );

            $this->db->trans_start();

            $this->db->where(array(
                'employeeNo' => $empID,
                'id' => $accountID
            ))->update('srp_erp_non_pay_salaryaccounts', $data);

            $this->db->trans_complete();

            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                echo json_encode(['s', 'Record updated successfully']);
            } else {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in update process']);
            }

        }
    }

    function delete_nonPayBankAccount()
    {
        $this->form_validation->set_rules('empID', 'Account Holder Name', 'trim|required');
        $this->form_validation->set_rules('accountID', 'Update ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {

            $empID = $this->input->post('empID');
            $accountID = $this->input->post('accountID');
            $this->db->trans_start();

            $this->db->where(array(
                'employeeNo' => $empID,
                'id' => $accountID
            ))->delete('srp_erp_non_pay_salaryaccounts');

            $this->db->trans_complete();

            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                echo json_encode(['s', 'Record deleted successfully']);
            } else {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in delete process']);
            }

        }
    }

    function getNopay_amount()
    {
        $empID = $this->input->post('empID');
        $attendanceDate = $this->input->post('attendanceDate');
        $presentType = $this->input->post('presentType');

        /**/
        $companyID = current_companyID();

        if ($presentType == 6) {


            $detail = array();
            $detail_arr = $this->db->query("SELECT *, masterTB.id AS masterID
                                 FROM srp_erp_nopaysystemtable AS masterTB
                                 INNER JOIN srp_erp_nopayformula AS fromulaTB ON fromulaTB.nopaySystemID =masterTB.id AND companyID={$companyID} ")->result_array();
            if ($detail_arr) {
                foreach ($detail_arr as $key => $row) {

                    $isNonPayroll = $row['isNonPayroll'];
                    $table = ($isNonPayroll != 'Y') ? 'srp_erp_pay_salarydeclartion' : 'srp_erp_non_pay_salarydeclartion';
                    $classTitle = explode(' ', $row['description']);
                    $formulaText = '';
                    $formula = trim($row['formulaString']);
                    $lastInputType = '';
                    $formulaBuilder = $this->formulaBuilder_to_sql_OT($formula);
                    $formulaDecodeOT = $formulaBuilder['formulaDecode'];
                    $select_str2 = $formulaBuilder['select_str2'];
                    $whereInClause = $formulaBuilder['whereInClause'];

                    $as = $this->db->query("SELECT calculationTB.employeeNo, '$isNonPayroll' AS type, (({$formulaDecodeOT } ) )AS transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT . " ) / companyLocalER) , companyLocalCurrencyDecimalPlaces  )AS localAmount, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT . " ) / companyReportingER)   , companyReportingCurrencyDecimalPlaces  )AS reportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces, seg.segmentID, seg.segmentCode FROM ( SELECT employeeNo, " . $select_str2 . " , transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces FROM {$table} AS salDec JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID = salDec.salaryCategoryID AND salCat.companyID ={$companyID} WHERE salDec.companyID = {$companyID}  AND employeeNo={$empID} AND salDec.salaryCategoryID  IN (" . $whereInClause . ") GROUP BY employeeNo, salDec.salaryCategoryID ) calculationTB JOIN srp_employeesdetails AS emp ON emp.EIdNo = calculationTB.employeeNo AND emp.Erp_companyID = {$companyID} JOIN srp_erp_segment seg ON seg.segmentID = emp.segmentID AND seg.companyID = {$companyID} GROUP BY employeeNo")->row_array();

                    /*$detail[$key]['type']= $as['type'];
                    $detail[$key]['transactionAmount']= $as['transactionAmount'];*/

                    if ($row['isNonPayroll'] == 'N') {
                        $detail['noPayAmount'] = ($as['transactionAmount'] != '' ? $as['transactionAmount'] : 0);
                    }
                    if ($row['isNonPayroll'] == 'Y') {
                        $detail['noPaynonPayrollAmount'] = ($as['transactionAmount'] != '' ? $as['transactionAmount'] : 0);
                    }

                }
                if (!empty($detail)) {
                    $this->db->where('empID', $empID);
                    $this->db->where('attendanceDate', $attendanceDate);
                    $this->db->update('srp_erp_pay_empattendancereview', $detail);

                    echo json_encode(array('s', $detail));
                    exit;
                } else {
                    echo json_encode(array('e', 'message' => 'Successfully updated'));
                    exit;
                }

            }
        } else {
            $detail = array('noPayAmount' => 0, 'noPaynonPayrollAmount' => 0);
            $this->db->where('empID', $empID);
            $this->db->where('attendanceDate', $attendanceDate);
            $this->db->update('srp_erp_pay_empattendancereview', $detail);
            echo json_encode(array('s', $detail));
            exit;
        }
    }


    function save_noPayFormula()
    {
        $this->form_validation->set_rules('payGroupID', 'Formula Master ID', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $formulaMasterID = $this->input->post('payGroupID');
            $formulaString = $this->input->post('formulaString');
            $salaryCategories = $this->input->post('salaryCategoryContainer');
            $salaryCategories = (trim($salaryCategories) == '') ? null : $salaryCategories;
            $ssoCategories = $this->input->post('SSOContainer');
            $ssoCategories = (trim($ssoCategories) == '') ? null : $ssoCategories;
            $payGroupCategories = $this->input->post('payGroupContainer');
            $payGroupCategories = (trim($payGroupCategories) == '') ? null : $payGroupCategories;

            $data = array(
                'formulaString' => $formulaString,
                'salaryCategories' => $salaryCategories,
                'ssoCategories' => $ssoCategories,
                'payGroupCategories' => $payGroupCategories,
                'modifiedPCID' => current_pc(),
                'modifiedUserID' => current_userID(),
                'modifiedUserName' => current_employee(),
                'modifiedDateTime' => current_date()
            );

            $this->db->trans_start();

            $this->db->where('companyID', current_companyID());
            $this->db->where('id', $formulaMasterID);
            $this->db->update('srp_erp_nopayformula', $data);


            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                echo json_encode(['s', 'Formula saved successfully']);
            } else {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in process']);
            }
        }
    }

    public function fetch_employees_template1()
    {
        $con = "IFNULL(Ename2, '')";

        $companyid = $this->common_data['company_data']['company_id'];
        $where = "t1.Erp_companyID = " . $companyid . "";
        $this->datatables->select('t1.EIdNo,t1.ECode,srp_titlemaster.TitleDescription as title,t1.EmpShortCode,t1.Ename3,t1.Ename2,t1.Ename1,t1.NIC,t1.EDOB,srp_erp_gender.name as gender,srp_nationality.Nationality,srp_erp_bloodgrouptype.BloodDescription,srp_erp_maritialstatus.description as MaritialStatus,DesDescription,srp_erp_employeemanagers.managerID,t1.EDOJ,srp_erp_systememployeetype.employeeType,t1.EpTelephone,t1.EcMobile,srp_countrymaster.CountryDes,t1.EEmail,srp_erp_segment.description as segment,t2.Ename2 as managerName,t1.EIdNo AS empHID', false)
            ->from('srp_employeesdetails AS t1')
            ->join('srp_designation', 'DesignationID=t1.EmpDesignationId', 'LEFT')
            ->join('srp_titlemaster', 'TitleID=t1.EmpTitleId', 'LEFT')
            ->join('srp_erp_gender', 'genderID=t1.Gender', 'LEFT')
            ->join('srp_nationality', 'srp_nationality.NId=t1.Nid', 'LEFT')
            ->join('srp_erp_bloodgrouptype', 'BloodTypeID=t1.BloodGroup', 'LEFT')
            ->join('srp_erp_maritialstatus', 'maritialstatusID=t1.MaritialStatus', 'LEFT')
            ->join('srp_erp_employeemanagers', 'empID=t1.EIdNo', 'LEFT')
            ->join('srp_erp_systememployeetype', 'employeeTypeID=t1.EmployeeConType', 'LEFT')
            ->join('srp_countrymaster', 'countryID=t1.EpAddress4', 'LEFT')
            ->join('srp_employeesdetails t2', 'srp_erp_employeemanagers.empID=t2.EIdNo', 'LEFT')
            ->join('srp_erp_segment', 'srp_erp_segment.segmentID=t1.segmentID', 'LEFT')
            ->add_column('action', '$1', 'empMaster_action(empHID, empName)')
            ->where($where);
        echo $this->datatables->generate();
    }

    function loadEmployees()
    {
        $companyID = current_companyID();
        $segmentArr = $this->input->post('segmentID');
        $segmentIN = "";

        if (!empty($segmentArr)) {
            $segmentIN = 'AND segmentID IN (' . join(",", $segmentArr) . ' )';
        }

        $result = $this->db->query("SELECT EIdNo,Ename2 FROM srp_employeesdetails
                                    WHERE Erp_companyID={$companyID} {$segmentIN}")->result_array();
        $data['employees'] = $result;

        echo $this->load->view('system/hrm/ajax/employee_dropdown', $data, true);

    }

    /**
     *
     */
    function employeesByLeavepolicy()
    {
        $policyType = $this->input->post('policyType');
        $companyID = current_companyID();
        $employees_arr = array();
        if ($policyType != '') {
            $policyCondition = ($policyType == 2)? "policyMasterID=$policyType" : "policyMasterID IN (1,3)";
            $employees = $this->db->query("SELECT EIdNo, Ename2, ECode FROM `srp_employeesdetails` 
                          INNER JOIN `srp_erp_leavegroupdetails`  ON `srp_erp_leavegroupdetails`.leaveGroupID = srp_employeesdetails.leaveGroupID AND {$policyCondition}
                          WHERE Erp_companyID = {$companyID} AND isDischarged != 1 AND empConfirmedYN=1 AND isSystemAdmin!=1 GROUP BY EIdNo")->result_array();
        }
        if (isset($employees)) {
            foreach ($employees as $row) {

                $employees_arr[trim($row['EIdNo'])] = trim($row['ECode']) . ' | ' . trim($row['Ename2']);
            }
        }
        echo form_dropdown('empID[]', $employees_arr, '', 'id="empID" multiple="multiple" class="form-control mid-width wrapItems "');

    }


    /** Common function for payroll and non payroll employee bank account save | update | delete **/
    function save_empBankAccounts()
    {

        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('accHolder', 'Account Holder Name', 'trim|required');
        $this->form_validation->set_rules('accountNo', 'Bank Account Number', 'trim|required');
        $this->form_validation->set_rules('salPerc', 'Salary Transfer %', 'trim|required');
        $this->form_validation->set_rules('bank_id', 'Bank', 'trim|required');
        $this->form_validation->set_rules('branch_id', 'Bank Branch', 'trim|required');
        $this->form_validation->set_rules('payrollType[]', 'Payroll Type', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {

            $payrollType = $this->input->post('payrollType');
            $accHolder = $this->input->post('accHolder');
            $bank_id = $this->input->post('bank_id');
            $br_name = $this->input->post('branch_id');
            $accountNo = $this->input->post('accountNo');
            $swiftCode = $this->input->post('swiftCode');
            $salPerc = $this->input->post('salPerc');
            $empID = $this->input->post('empID');

            $data = array(
                'employeeNo' => $empID,
                'bankID' => $bank_id,
                'isActive' => 1,
                'accountNo' => $accountNo,
                'accountHolderName' => $accHolder,
                'branchID' => $br_name,
                'swiftCode' => $swiftCode,
                'toBankPercentage' => $salPerc,
                'companyID' => $this->common_data['company_data']['company_id'],
                'companyCode' => $this->common_data['company_data']['company_code'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdDateTime' => current_date()
            );

            $this->db->trans_start();

            foreach ($payrollType as $payrollTypeRow) {
                $table = ($payrollTypeRow == 1) ? 'srp_erp_pay_salaryaccounts' : 'srp_erp_non_pay_salaryaccounts';
                $this->db->insert($table, $data);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() > 0) {
                $this->db->trans_commit();
                echo json_encode(['s', 'Salary Account Saved']);
            } else {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in process']);
            }

        }
    }

    function update_empBankAccounts()
    {
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('accHolder', 'Account Holder Name', 'trim|required');
        $this->form_validation->set_rules('accountNo', 'Bank Account Number', 'trim|required');
        $this->form_validation->set_rules('salPerc', 'Salary Transfer %', 'trim|required');
        $this->form_validation->set_rules('bank_id', 'Bank', 'trim|required');
        $this->form_validation->set_rules('branch_id', 'Bank Branch', 'trim|required');
        $this->form_validation->set_rules('accountID', 'Update ID', 'trim|required');
        $this->form_validation->set_rules('payrollType-in-update', 'Payroll Type', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {

            $accountID = $this->input->post('accountID');
            $payrollType = $this->input->post('payrollType-in-update');
            $accHolder = $this->input->post('accHolder');
            $bank_id = $this->input->post('bank_id');
            $br_name = $this->input->post('branch_id');
            $accountNo = $this->input->post('accountNo');
            $swiftCode = $this->input->post('swiftCode');
            $salPerc = $this->input->post('salPerc');
            $empID = $this->input->post('empID');
            $accStatus = $this->input->post('accStatus');

            $data = array(
                'employeeNo' => $empID,
                'bankID' => $bank_id,
                'accountNo' => $accountNo,
                'accountHolderName' => $accHolder,
                'branchID' => $br_name,
                'swiftCode' => $swiftCode,
                'toBankPercentage' => $salPerc,
                'isActive' => $accStatus,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => current_date()
            );

            $this->db->trans_start();

            $table = ($payrollType == 1) ? 'srp_erp_pay_salaryaccounts' : 'srp_erp_non_pay_salaryaccounts';
            $this->db->where('companyID', current_companyID())
                ->where('id', $accountID)
                ->update($table, $data);

            $this->db->trans_complete();

            if ($this->db->trans_status() > 0) {
                $this->db->trans_commit();
                echo json_encode(['s', 'Bank account updated']);
            } else {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in bank account update process']);
            }

        }
    }

    function delete_empBankAccounts()
    {
        $this->form_validation->set_rules('accountID', 'Update ID', 'trim|required');
        $this->form_validation->set_rules('payrollType', 'Payroll Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {

            $payrollType = $this->input->post('payrollType');
            $accountID = $this->input->post('accountID');

            $table = ($payrollType == 1) ? 'srp_erp_pay_salaryaccounts' : 'srp_erp_non_pay_salaryaccounts';
            $this->db->where('companyID', current_companyID())
                ->where('id', $accountID)
                ->delete($table);

            if ($this->db->affected_rows() > 0) {
                echo json_encode(['s', 'Bank account deleted']);
            } else {
                echo json_encode(['e', 'Error in bank account delete process']);
            }

        }
    }

    function moduledetail()
    {

        echo $this->load->view('system/erp_appsdetails', '', true);

    }

    function moduleView()
    {
        $data['extra'] = '';
        $data['title'] = 'List of Module';
        $data['main_content'] = 'system/erp_apps';
        $this->load->view('include/templateModule', $data);
    }

    function modulesdetails()
    {
        $data['extra'] = $this->uri->segment('2');
        $data['title'] = 'List of Module';
        $data['main_content'] = 'system/erp_appsdetails';
        $this->load->view('include/templateModule', $data);
    }

    function unloackUser()
    {
        echo json_encode($this->Employee_model->unloackUser());
    }

    function requestQuote()
    {

        $this->form_validation->set_rules('requestName', 'Your Name', 'trim|required');
        $this->form_validation->set_rules('phoneNumber', 'Phone Number', 'trim|required|numeric');
        $this->form_validation->set_rules('companyName', 'Company Name', 'trim|required');
        $this->form_validation->set_rules('country', 'country ', 'trim|required');
        $this->form_validation->set_rules('emailID', 'emailID', 'trim|required|valid_email');
        $this->form_validation->set_rules('aboutUs', 'about Us', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {

            $erpSystemDescription = $this->input->post('erpSystemDescription');
            $requestName = $this->input->post('requestName');
            $companyName = $this->input->post('companyName');
            $emailID = $this->input->post('emailID');
            $phoneNumber = $this->input->post('phoneNumber');
            $aboutUsDescription = $this->input->post('aboutUsDescription');

            $x = 0;
            $params[$x]["companyID"] = current_companyID();
            $params[$x]["documentID"] = '';
            $params[$x]["documentSystemCode"] = '';
            $params[$x]["documentCode"] = '';
            $params[$x]["emailSubject"] = 'Requested Quote';
            $params[$x]["empEmail"] = 'info@xlouderp.com';
            $params[$x]["empID"] = current_userID();
            $params[$x]["empName"] = $requestName;
            $params[$x]["emailBody"] = "<p>Dear Admin,<br /><br /></p> <p>Requested Addon : <strong>{$erpSystemDescription}</strong></p> <p>Requested User : <strong>{$requestName}</strong></p> <p>Company : <strong>{$companyName}</strong></p> <p>Country : <strong>as</strong></p> <p>Email : <strong>{$emailID}</strong></p> <p>Phone Number :<strong> {$phoneNumber}</strong></p> <p>Heard from : <strong>{$aboutUsDescription}</strong></p> <p>&nbsp;</p> ";

            if (!empty($params)) {
                $this->email_manual->set_email_detail($params);

                echo json_encode(['s', 'Successfully Requested']);
            }
        }
    }

    function get_empSalaryInCategory()
    {
        $companyID = current_companyID();
        $empID = $this->input->post('empID');
        $catID = $this->input->post('catID');
        $payrollType = $this->input->post('payrollType');
        $effectiveDate = $this->input->post('effectiveDate');
        $declarationTb = ($payrollType == 1) ? 'srp_erp_pay_salarydeclartion' : 'srp_erp_non_pay_salarydeclartion';
        $date_format_policy = date_format_policy();
        $effectiveDate = input_format_date($effectiveDate, $date_format_policy);


        /*$totalEntitle = $this->db->query("SELECT IF( ISNULL(SUM(amount)), round(0,transactionCurrencyDecimalPlaces) , FORMAT(SUM(amount), transactionCurrencyDecimalPlaces))
                                           AS totalEntitle FROM srp_erp_pay_salarydeclartion
                                          WHERE effectiveDate <= '{$effectiveDate}' AND employeeNo={$empID} AND salaryCategoryID={$catID} AND
                                          companyID={$companyID}")->row('totalEntitle');*/

        $totalEntitle = $this->db->query("SELECT IF( ISNULL(SUM(amount)), round(0,transactionCurrencyDecimalPlaces) , FORMAT(SUM(amount), transactionCurrencyDecimalPlaces))
                                           AS totalEntitle FROM {$declarationTb}
                                          WHERE  employeeNo={$empID} AND salaryCategoryID={$catID} AND
                                          companyID={$companyID}")->row('totalEntitle');
        echo json_encode($totalEntitle);
    }

    public function save_machineMapping()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->input->post('dbYN') == 1) {
            $this->form_validation->set_rules('dbhost', 'DB Host', 'trim|required');
            $this->form_validation->set_rules('dbname', 'DB Name', 'trim|required');
            $this->form_validation->set_rules('dbpassword', 'DB Password', 'trim|required');
            $this->form_validation->set_rules('dbuser', 'DB User', 'trim|required');
            $this->form_validation->set_rules('dbtableName', 'DB Table Name', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_machineMapping());
        }
    }

    public function fetch_machineType()
    {
        $this->datatables->select('machineMasterID AS ID , description', false)
            ->from('srp_erp_machinemaster')
            ->add_column('action', ' <a onclick="edit_machinMapping($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_machinMapping($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>', 'ID, description');
        echo $this->datatables->generate();
    }


    function delete_machine_master()
    {
        $machineMasterID = $this->input->post('machineMasterID');
        $this->db->where('machineMasterID', $machineMasterID)->delete('srp_erp_machinemaster');

        if ($this->db->affected_rows() > 0) {
            $this->db->where('machineMasterID', $machineMasterID)->delete('srp_erp_machinedetail');
            echo json_encode(['s', 'Machine type deleted']);
        } else {
            echo json_encode(['e', 'Error in machine type delete process']);
        }

    }

    public function fetch_machinedetail()
    {
        $machineMasterID = $this->input->post('machineMasterID');
        $this->datatables->select('machineDetailID as ID,machineMasterID,sortOrder,columnName as description,srp_erp_machinedetail.machineTypeID as  machineTypeID', false)
            ->from('srp_erp_machinedetail')
            ->join('srp_erp_machinetype', 'srp_erp_machinedetail.machineTypeID =srp_erp_machinetype.machineTypeID', 'LEFT')
            ->where('machineMasterID', $machineMasterID)
            ->add_column('action', '<a onclick="delete_machinMappingdetail($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>', 'ID, description')
            ->add_column('sortOrderdesc', '$1', 'edit_machine_type(sortOrder,machineMasterID,ID)')
            ->add_column('columnMapping', '$1', 'edit_machine_mapping(machineTypeID,machineMasterID,ID)');
        echo $this->datatables->generate();
    }

    public function save_machineMapping_detail()
    {
        $this->form_validation->set_rules('columnName', 'Column Name', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {
            echo json_encode($this->Employee_model->save_machineMapping_detail());
        }
    }

    public function update_machineMapping_detail()
    {
        $this->form_validation->set_rules('value', 'Value', 'trim|required');
        $this->form_validation->set_rules('masterID', 'masterID', 'trim|required');
        $this->form_validation->set_rules('detailID', 'detailID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {
            echo json_encode($this->Employee_model->update_machineMapping_detail());
        }
    }

    public function delete_machine_detail()
    {
        $machineDetailID = $this->input->post('machineDetailID');
        $this->db->where('machineDetailID', $machineDetailID)->delete('srp_erp_machinedetail');

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['s', 'Machine detail deleted']);
        } else {
            echo json_encode(['e', 'Error in machine type delete process']);
        }
    }

    public function update_machineMappingcolumn_detail()
    {
        $this->form_validation->set_rules('masterID', 'masterID', 'trim|required');
        $this->form_validation->set_rules('detailID', 'detailID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {
            echo json_encode($this->Employee_model->update_machineMappingcolumn_detail());
        }
    }

    public function fetch_employeeEnvoy()
    {
        $employee_filter = '';
        $segment_filter = '';
        $employee = $this->input->post('employeeCode');
        $segment = $this->input->post('segment');
        $isDischarged = $this->input->post('isDischarged');
        if (!empty($employee) && $employee != 'null') {
            $employee = array($this->input->post('employeeCode'));
            $whereIN = "( " . join("' , '", $employee) . " )";
            $employee_filter = " AND EIdNo IN " . $whereIN;
        }

        if (!empty($segment) && $segment != 'null') {
            $segment = array($this->input->post('segment'));
            $whereIN = "( " . join("' , '", $segment) . " )";
            $segment_filter = " AND t1.segmentID IN " . $whereIN;
        }
//echo '$segment_filter: '.$segment_filter;

        switch ($isDischarged) {
            case 'N':
                $discharged_filter = ' AND isDischarged != 1';
                break;

            case 'Y':
                $discharged_filter = ' AND isDischarged = 1';
                break;

            default:
                $discharged_filter = '';
        }

        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $where = "t1.Erp_companyID = " . $companyID . $employee_filter . $segment_filter . $discharged_filter . "";

        $this->datatables->select('EIdNo,EmpSecondaryCode,EmpSecondaryCode AS empShtrCode,ECode,Ename1,Ename2,Ename3,Ename4,initial,EmpShortCode,EpAddress1,EpAddress2,
                EpAddress3,EpTelephone,EcPOBox,EcMobile,TitleDescription,srp_erp_segment.description as segment,CountryDes,BloodDescription,
                DesDescription,NIC,maritialStatus.description AS empMaritialStatus,managerCode,DATE_FORMAT(EDOB,\'' . $convertFormat . '\') AS dob,
                DATE_FORMAT(EDOJ,\'' . $convertFormat . '\') AS doj,employeeType,IF(empConfirmedYN=1,\'Yes\',\'No\') AS  empConfirmedYNStatus,
                IF(Gender=1,\'Male\', \'Female\') AS genderStr, IF(isDischarged=1, \'Discharged\', \'Active\') AS empStatus, socialInsuranceNumber,
                DATE_FORMAT(contractStartDate,\'' . $convertFormat . '\') AS contractStart, DATE_FORMAT(contractEndDate,\'' . $convertFormat . '\')
                AS contractEnd
                ', false)
            ->from('srp_employeesdetails AS t1')
            ->join('srp_designation', 'DesignationID=t1.EmpDesignationId', 'LEFT')
            ->join('srp_titlemaster', 'TitleID=EmpTitleId')
            ->join('srp_erp_segment', 'srp_erp_segment.segmentID=t1.segmentID', 'LEFT')
            ->join('srp_countrymaster', 'srp_countrymaster.countryID=t1.EpAddress4', 'LEFT')
            ->join('srp_nationality', 'srp_nationality.NId=t1.Nid', 'LEFT')
            ->join('srp_erp_bloodgrouptype', 'srp_erp_bloodgrouptype.BloodTypeID=t1.BloodGroup', 'LEFT')
            ->join('srp_erp_maritialstatus AS maritialStatus', 'maritialStatus.maritialstatusID=t1.MaritialStatus', 'LEFT')
            ->join('srp_erp_systememployeetype AS employeeType', 'employeeType.employeeTypeID=t1.EmployeeConType', 'LEFT')
            ->join('(SELECT empID, EmpSecondaryCode AS managerCode FROM  srp_erp_employeemanagers
                    JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo=srp_erp_employeemanagers.managerID
                    WHERE companyID=' . $companyID . ' AND active=1) AS repotingManagerTB', 'repotingManagerTB.empID=t1.EIdNo', 'LEFT')
            ->join('(SELECT empID, socialInsuranceNumber FROM srp_erp_socialinsurancedetails WHERE companyID=' . $companyID . ' GROUP BY empID)
                     AS ssoTB', 'ssoTB.empID=t1.EIdNo', 'LEFT')
            ->add_column('img', '<center><img class="" src="$1" style="width:30px;height: 20px;" ></center>', 'empImage(EmpImage)')
            ///->add_column('secondaryCodeStr', '<center>$1</center>', 'empMaster_action(EIdNo,empShtrCode,1)')
            ->add_column('secondaryCodeStr', '$1', 'empShtrCode')
            ->add_column('EmpShortCodeStr', '<center>$1</center>', 'empMaster_action(EIdNo,EmpShortCode,1)')
            ->add_column('confirmedStr', '<center>$1</center>', 'empConfirmedYNStatus')
            ->add_column('action', '$1', 'empMaster_action(EIdNo, empName)')
            ->where('isSystemAdmin !=', 1)
            ->where($where);
        echo $this->datatables->generate();
    }


    function fetch_empSalaryDeclaration()
    {
        $empId = trim($this->input->post('empId'));
        $isNonPayroll = trim($this->input->post('isNonPayroll'));
        $decTable = ($isNonPayroll == 'Y') ? 'srp_erp_non_pay_salarydeclartion' : 'srp_erp_pay_salarydeclartion';
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('id, salaryDescription, FORMAT(amount,declartionTB.transactionCurrencyDecimalPlaces) AS amountTr, narration,
                                   DATE_FORMAT(effectiveDate,\'' . $convertFormat . '\') AS effectiveDate,
                                   DATE_FORMAT(payDate,\'' . $convertFormat . '\') AS payDate, documentSystemCode', false)
            ->from($decTable . ' declartionTB')
            ->join('srp_erp_pay_salarycategories AS catTB', 'catTB.salaryCategoryID = declartionTB.salaryCategoryID')
            ->join('srp_erp_salarydeclarationmaster AS decMaster', 'decMaster.salarydeclarationMasterID=declartionTB.sdMasterID')
            ->add_column('amountTrAlign', '<div align="right"> $1</div>', 'amountTr')
            ->add_column('effectiveDateStr', '<div align="center"> $1</div>', 'effectiveDate')
            ->add_column('payDateStr', '<div align="center"> $1</div>', 'payDate')
            ->where('employeeNo', $empId)
            ->where('declartionTB.companyID', current_companyID());
        echo $this->datatables->generate();
    }


    function attendance_export_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Attendance list');
        $this->load->database();
        $users = $this->Employee_model->fetch_employees_for_excel();

        /*POST & query*/
        $companyID = current_companyID();

        $attendanceDate = $this->input->post('attendanceDate');
        $approvedYN = $this->input->post('approvedYN');
        $managerId = current_userID();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $companyID = current_companyID();
        $floorID = $this->input->post('floorID');

        $where = "";
        $where .= ($approvedYN != '' ? " AND approvedYN ={$approvedYN}" : "");
        $where .= ($datefrom != '' ? " AND attendanceDate >='{$datefrom}'" : "");
        $where .= ($dateto != '' ? " AND attendanceDate <='{$dateto}'" : "");
        $managerID = current_userID();

        $hrAdmin = $this->db->query("Select * from srp_employeesdetails where isHRAdmin=1 AND EIdNo={$managerID}")->row_array();
        if (empty($hrAdmin)) {
            $qry = "SELECT ROUND(noPayAmount, 2) as noPayAmount,ROUND(noPaynonPayrollAmount, 2) as noPaynonPayrollAmount,isWeekEndDay,ROUND(paymentOT, 2) as paymentOT,approvedComment,approvedYN, srp_erp_pay_empattendancereview.empID, ECode, Ename1, Ename2, empMachineID, floorDescription, ID, machineID, srp_erp_pay_empattendancereview.floorID, attendanceDate, presentTypeID, DATE_FORMAT(checkIn, '%h:%i %p') checkIn, DATE_FORMAT(checkOut, '%h:%i %p') checkOut, DATE_FORMAT(onDuty, '%h:%i %p') onDuty, DATE_FORMAT(offDuty, '%h:%i %p') offDuty, lateHours, earlyHours, OTHours, mustCheck, normalTime,normalDay, weekend, holiday, NDaysOT, weekendOTHours, holidayOTHours, realTime FROM srp_erp_pay_empattendancereview LEFT JOIN `srp_employeesdetails` ON srp_erp_pay_empattendancereview.empID = srp_employeesdetails.EIdNo LEFT JOIN `srp_erp_pay_floormaster` ON srp_erp_pay_floormaster.floorID = srp_erp_pay_empattendancereview.floorID INNER JOIN `srp_erp_employeemanagers` on srp_erp_employeemanagers.empID=srp_employeesdetails.EIdNo WHERE attendanceDate = '{$attendanceDate}' AND srp_erp_pay_empattendancereview.companyID = {$companyID} AND confirmedYN = 1 AND srp_erp_employeemanagers.level=0 AND srp_erp_employeemanagers.active=1 AND managerID={$managerId} AND srp_erp_pay_empattendancereview.floorID=$floorID";
        } else {
            /*hradmin*/
            $qry = "SELECT ROUND(noPayAmount, 2) as noPayAmount,ROUND(noPaynonPayrollAmount, 2) as noPaynonPayrollAmount,isWeekEndDay,ROUND(paymentOT, 2) as paymentOT,approvedComment,approvedYN, srp_erp_pay_empattendancereview.empID, ECode, Ename1, Ename2, empMachineID, floorDescription, ID, machineID, srp_erp_pay_empattendancereview.floorID, attendanceDate, presentTypeID, DATE_FORMAT(checkIn, '%h:%i %p') checkIn, DATE_FORMAT(checkOut, '%h:%i %p') checkOut, DATE_FORMAT(onDuty, '%h:%i %p') onDuty, DATE_FORMAT(offDuty, '%h:%i %p') offDuty, lateHours, earlyHours, OTHours, mustCheck, normalTime,normalDay, weekend, holiday, NDaysOT, weekendOTHours, holidayOTHours, realTime FROM srp_erp_pay_empattendancereview LEFT JOIN `srp_employeesdetails` ON srp_erp_pay_empattendancereview.empID = srp_employeesdetails.EIdNo LEFT JOIN `srp_erp_pay_floormaster` ON srp_erp_pay_floormaster.floorID = srp_erp_pay_empattendancereview.floorID INNER JOIN `srp_erp_employeemanagers` on srp_erp_employeemanagers.empID=srp_employeesdetails.EIdNo WHERE attendanceDate = '{$attendanceDate}' AND srp_erp_pay_empattendancereview.companyID = {$companyID} AND confirmedYN = 1 AND srp_erp_pay_empattendancereview.floorID=$floorID";
        }

        $qry2 = "SELECT ROUND(noPayAmount, 2) as noPayAmount,ROUND(noPaynonPayrollAmount, 2) as noPaynonPayrollAmount, isWeekEndDay,ROUND(paymentOT, 2) as paymentOT,approvedComment,approvedYN, srp_erp_pay_empattendancereview.empID, ECode, Ename1, Ename2, empMachineID, floorDescription, ID, machineID, srp_erp_pay_empattendancereview.floorID, attendanceDate, presentTypeID, DATE_FORMAT(checkIn, '%h:%i %p') checkIn, DATE_FORMAT(checkOut, '%h:%i %p') checkOut, DATE_FORMAT(onDuty, '%h:%i %p') onDuty, DATE_FORMAT(offDuty, '%h:%i %p') offDuty, lateHours, earlyHours, OTHours,  mustCheck, normalTime,normalDay, weekend, holiday, NDaysOT, weekendOTHours, holidayOTHours, realTime FROM srp_erp_pay_empattendancereview LEFT JOIN `srp_employeesdetails` ON srp_erp_pay_empattendancereview.empID = srp_employeesdetails.EIdNo LEFT JOIN `srp_erp_pay_floormaster` ON srp_erp_pay_floormaster.floorID = srp_erp_pay_empattendancereview.floorID INNER JOIN `srp_erp_employeemanagers` on srp_erp_employeemanagers.empID=srp_employeesdetails.EIdNo WHERE  srp_erp_pay_empattendancereview.companyID = {$companyID} $where AND confirmedYN = 1 AND srp_erp_employeemanagers.level=0 AND srp_erp_employeemanagers.active=1 AND managerID={$managerId}";

        if ($this->input->post('col') == 'employee') {
            $qry = $qry2;
        }

        $tempAttData = $this->db->query($qry)->result_array();
        if (!empty($tempAttData)) {

            foreach ($tempAttData as $key => $val) {
                $totWorkingHours = '';
                $attendhours = '';
                $isAllSet = 0;
                if ($val['checkIn'] != null && $val['checkOut'] != null && $val['offDuty'] != null) {
                    $datetime1 = new DateTime($val['offDuty']);

                    if ($val['onDuty'] >= $val['checkIn']) {
                        $datetime2 = new DateTime($val['onDuty']);
                    } else {
                        $datetime2 = new DateTime($val['checkIn']);
                    }
                    $totWorkingHours_obj = $datetime1->diff($datetime2);
                    $totWorkingHours = $totWorkingHours_obj->format('%h') . " h &nbsp;&nbsp;" . $totWorkingHours_obj->format('%i') . " m";
                }
                if ($val['checkIn'] != null && $val['checkOut'] != null) {
                    $datetime1 = new DateTime($val['checkIn']);
                    $datetime2 = new DateTime($val['checkOut']);
                    $attendhours_obj = $datetime1->diff($datetime2);
                    $attendhours = $attendhours_obj->format('%h') . " h &nbsp;&nbsp;" . $attendhours_obj->format('%i') . " m";
                }
                $data[$key]['approvedYN'] = ($val['approvedYN'] == 1 ? 'Yes' : 'No');
                $data[$key]['Comment'] = $val['approvedComment'];
                $data[$key]['EMPCode'] = $val['ECode'];
                $data[$key]['EmpName'] = $val['Ename2'];
                $data[$key]['Date'] = $val['attendanceDate'];
                $data[$key]['Department'] = $val['floorDescription'];
                $data[$key]['OnDutyTime'] = ($val['onDuty'] == null) ? '-not set-' : $val['onDuty'];

                $data[$key]['OffDutyTime'] = ($val['offDuty'] == null) ? '-not set-' : $val['offDuty'];
                $data[$key]['ClockIn'] = $val['checkIn'];
                $data[$key]['ClockOut'] = $val['checkOut'];
                $data[$key]['NormalTime'] = $val['normalTime'];
                $data[$key]['RealTime'] = $val['realTime'];
                $data[$key]['Present'] = $val['presentTypeID'];
                $data[$key]['Late'] = gmdate("H:i", $val['lateHours'] * 60);
                $data[$key]['Early'] = gmdate("H:i", $val['earlyHours'] * 60);
                $data[$key]['OTTime'] = gmdate("H:i", $val['OTHours'] * 60);
                $data[$key]['WorkTime'] = $totWorkingHours;
                $data[$key]['NDay'] = $val['normalDay'];
                $data[$key]['WeekEnd'] = $val['weekend'];
                $data[$key]['Holiday'] = $val['holiday'];
                $data[$key]['ATT_Time'] = $attendhours;
                $data[$key]['NDaysOT'] = gmdate("H:i", $val['NDaysOT'] * 60);
                $data[$key]['WeekendOT'] = gmdate("H:i", $val['weekendOTHours'] * 60);
                $data[$key]['HolidayOT'] = gmdate("H:i", $val['holidayOTHours'] * 60);
                $data[$key]['OT Amount'] = $val['paymentOT'];
                $data[$key]['NoPayAmount'] = $val['noPayAmount'];
                $data[$key]['NoPaynonPayrollAmount'] = $val['noPaynonPayrollAmount'];

            }
        }

        /**/

        // Header
        $this->excel->getActiveSheet()->fromArray(array('ApprovedYN', 'Comment', 'EMP Code', 'Emp Name', 'Date', 'Department', 'On Duty Time', 'Off Duty Time', 'Clock In', 'Clock Out', 'Normal Time', 'Real Time', 'Present', 'Late', 'Early', 'OT Time', 'Work Time', 'NDay', 'Week End', 'Holiday', 'ATT_Time', 'NDays OT', 'Weekend OT', 'Holiday OT', 'OT Amount', 'No Pay Amount', 'No Pay non Payroll Amount'), NULL, 'A1');
        // Data
        $this->excel->getActiveSheet()->fromArray($data, null, 'A2');
        //set aligment to center for that merged cell (A1 to D1)
        $filename = 'Attendance Details - ' . $attendanceDate . ' .xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }

    /*Fixed salary declaration added by Nazir*/
    function fetch_fixed_element_salaryDeclaration()
    {
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("fedeclarationMasterID,Description,documentSystemCode,transactionCurrency,DATE_FORMAT(documentDate,' $convertFormat ') AS newDocumentDate,transactionCurrency,Description,confirmedYN,approvedYN,createdUserID");
        $this->datatables->from('srp_erp_ot_fixedelementdeclarationmaster');
        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('confirmed', '$1', 'confirm(confirmedYN)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"FED",fedeclarationMasterID)');
        $this->datatables->add_column('edit', '$1', 'load_fixedElementDeclaration_action(fedeclarationMasterID, confirmedYN, approvedYN, createdUserID)');
        echo $this->datatables->generate();
    }

    /*Fixed salary declaration added by Nazir*/
    function save_fixed_element_salaryDeclaration()
    {
        $this->form_validation->set_rules('MasterCurrency', 'Currency', 'trim|required');
        $this->form_validation->set_rules('salary_description', 'Description', 'trim|required');
        $this->form_validation->set_rules('documentDate', 'Document date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_fixed_element_salaryDeclaration());
        }
    }


    /*Fixed salary declaration added by Nazir*/
    function fetch_fixedElementDeclaration_Master()
    {
        $id = $this->input->post('id');
        $result = $this->Employee_model->fetch_FixedElementDeclarationMaster($id);
        if (!empty($result)) {
            $data['output'] = $result;
            echo $this->load->view('system/hrm/OverTimeManagementSalamAir/fixed_element_Declaration_detail', $data, true);
        } else {
            return false;
        }
    }

    function get_empFixedElementTotal()
    {
        $companyID = current_companyID();
        $empID = $this->input->post('empID');
        $catID = $this->input->post('catID');
        $effectiveDate = $this->input->post('effectiveDate');

        $date_format_policy = date_format_policy();
        $effectiveDate = input_format_date($effectiveDate, $date_format_policy);

        $totalEntitle = $this->db->query("SELECT IF( ISNULL(SUM(amount)), round(0,transactionCurrencyDecimalPlaces) , FORMAT(SUM(amount), transactionCurrencyDecimalPlaces))
                                       AS totalEntitle FROM srp_erp_ot_pay_fixedelementdeclration
                                      WHERE  employeeNo={$empID} AND fixedElementID={$catID} AND
                                      companyID={$companyID}")->row('totalEntitle');
        echo json_encode($totalEntitle);
    }

    function save_fixed_element_declaration_detail()
    {
        echo json_encode($this->Employee_model->save_fixed_element_declaration());
    }

    function load_fixedElement_declaration_drilldown_table()
    {
        echo json_encode($this->Employee_model->load_fixedElement_declaration_drilldown_table());
    }

    function load_fixed_elementDeclaration_approval_confirmation()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('feDeclarationMasterID'));
        $data['extra'] = $this->Employee_model->fetch_FixedElementDeclarationMaster($masterID);
        $html = $this->load->view('system/hrm/OverTimeManagementSalamAir/fixed_elementDeclaration_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function ConfirmFixedElementDeclaration()
    {
        echo json_encode($this->Employee_model->ConfirmFixedElementDeclaration());
    }

    function delete_fixed_element_declaration_master()
    {
        echo json_encode($this->Employee_model->delete_fixed_element_declaration_master());
    }

    function referback_fixed_element_declaration()
    {
        $masterID = $this->input->post('masterID');
        $masterDetail = $this->Employee_model->fetch_FixedElementDeclarationMaster($masterID);

        if ($masterDetail['approvedYN'] == 1) {
            echo json_encode(array('e', 'This document is already approved.<p>You can not refer back this.'));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($masterID, 'FED');
            if ($status == 1) {
                echo json_encode(array('s', $masterDetail['documentSystemCode'] . ' Referred Back Successfully.'));
            } else {
                echo json_encode(array('e', $masterDetail['documentSystemCode'] . ' Error in refer back.'));
            }
        }
    }

    /** Over-time management for Salam-Air **/
    function save_OT_monthAddition()
    {
        $this->form_validation->set_rules('dateDesc', 'Date', 'trim|required|date');
        $this->form_validation->set_rules('monthDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('currencyID', 'Currency', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $unProcessedEmployees = $this->payrollNotProcessedEmployees();

            if ($unProcessedEmployees[0] == 's') {
                echo json_encode($this->Employee_model->save_OT_monthAddition());
            } else {
                echo json_encode($unProcessedEmployees);
            }
        }
    }


    function load_monthlyOTAdditionMaster()
    {
        $convertFormat = convert_date_format_sql();

        $this->datatables->select('monthlyAdditionsMasterID AS masterID, monthlyAdditionsCode, description, DATE_FORMAT(dateMA,\'' . $convertFormat . '\') AS dateMA,
                            confirmedYN, isProcessed', false)
            ->from('srp_erp_ot_monthlyadditionsmaster')
            ->add_column('status', '$1', 'confirm(confirmedYN)')
            ->add_column('action', '$1', 'OT_monthlyAction(masterID, confirmedYN, isProcessed, monthlyAdditionsCode)')
            ->where('companyID', current_companyID());
        echo $this->datatables->generate();
    }

    function edit_OT_monthAddition()
    {
        echo json_encode($this->Employee_model->edit_OT_monthAddition());
    }

    function save_OT_employeeAsTemp()
    {
        $this->form_validation->set_rules('masterID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('dateDesc', 'Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $empHiddenID = $this->input->post('temp_empHiddenID');
            if (count($empHiddenID) > 0) {

                $companyID = current_companyID();
                $date_format_policy = date_format_policy();
                $dtDsc = $this->input->post('dateDesc');
                $empArr = $this->input->post('selectedEmployees');
                $dateDesc = input_format_date($dtDsc, $date_format_policy);
                $payYear = date('Y', strtotime($dateDesc));
                $payMonth = date('m', strtotime($dateDesc));
                $minDate = date('Y-m-01', strtotime($dateDesc));
                $entryDateLast = date('Y-m-t', strtotime($dateDesc));

                $masterData = $this->Employee_model->edit_OT_monthAddition();

                if ($masterData['isProcessed'] == 1) {
                    echo json_encode(['e', 'This document is already processed']);
                }

                if ($masterData['confirmedYN'] == 1) {
                    echo json_encode(['e', 'This document is already confirmed']);
                }


                /*** Check employee already pulled in a OT addition on selected month **/
                $isAlreadyPulled = $this->db->query("SELECT CONCAT(ECode,' - ', Ename2) AS empData
                                                     FROM srp_erp_ot_monthlyadditionsmaster AS addMaster
                                                     JOIN srp_erp_ot_monthlyadditiondetail AS addDetail
                                                     ON addDetail.monthlyAdditionsMasterID = addMaster.monthlyAdditionsMasterID
                                                     AND addDetail.companyID={$companyID}
                                                     JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = empID AND Erp_companyID=11
                                                     WHERE addMaster.companyID={$companyID} AND dateMA BETWEEN '{$minDate}'
                                                     AND '{$entryDateLast}' AND empID IN ({$empArr})")->result_array();

                if (!empty($isAlreadyPulled)) {
                    $employeesStr = implode('<br/>', array_column($isAlreadyPulled, 'empData'));

                    echo json_encode(array('e', 'Following employees are already added for <br/>monthly OT addition on selected Month <br/>' . $employeesStr));
                    exit;
                }

                $isPayrollProcessed = isPayrollProcessedForEmpGroup($empArr, $payYear, $payMonth, 'N');


                if (empty($isPayrollProcessed)) {
                    if (!empty($this->input->post('empHiddenID'))) {
                        $isProcessSuccess = $this->Employee_model->save_empMonthlyAdditionOT();
                    } else {
                        $isProcessSuccess[0] = 's';
                    }

                    if ($isProcessSuccess[0] == 's') {
                        echo json_encode($this->Employee_model->save_OT_employeeAsTemp());
                    } else {
                        echo json_encode($isProcessSuccess);
                    }

                } else {
                    $employeesStr = implode('<br/>', array_column($isPayrollProcessed, 'empData'));
                    echo json_encode(array('e', 'Payroll already processed on selected <br/> month (' . $dtDsc . ') for following employees <br/>' . $employeesStr));
                }

            } else {
                echo json_encode(array('e', 'There are no one selected to proceed'));
            }
        }

    }

    function fetch_fixed_element_declaration_approval()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */
        $approvedYN = trim($this->input->post('approvedYN'));
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('fedeclarationMasterID,srp_erp_ot_fixedelementdeclarationmaster.companyCode,srp_erp_ot_fixedelementdeclarationmaster.documentSystemCode,Description,transactionCurrency,confirmedYN,srp_erp_documentapproved . approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(srp_erp_ot_fixedelementdeclarationmaster.documentDate,\'' . $convertFormat . '\') AS newDocumentDate, srp_erp_ot_fixedelementdeclarationmaster.documentSystemCode AS docCodeSalaryDeclaration', false);
        $this->datatables->from('srp_erp_ot_fixedelementdeclarationmaster');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_ot_fixedelementdeclarationmaster.fedeclarationMasterID AND srp_erp_documentapproved.approvalLevelID = srp_erp_ot_fixedelementdeclarationmaster.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_ot_fixedelementdeclarationmaster.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'FED');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'FED');
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_ot_fixedelementdeclarationmaster.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN')));
        $this->datatables->add_column('purchaseOrderCode', '$1', 'approval_change_modal(purchaseOrderCode, purchaseOrderID, documentApprovedID, approvalLevelID, approvedYN, PO,0)');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('documentSystemCode_str', '<a onclick=\'fetchPage("system/hrm/OverTimeManagementSalamAir/fixed_element_declaration_new","$2","HRMS")\'> $1 </a>', 'docCodeSalaryDeclaration, fedeclarationMasterID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "FED", fedeclarationMasterID)');
        $this->datatables->add_column('edit', '$1', 'load_fixed_element_declaration_action_approval(fedeclarationMasterID, approvalLevelID, approvedYN, documentApprovedID)');

        echo $this->datatables->generate();
    }

    function save_fixed_element_declaration_approval()
    {
        $this->form_validation->set_rules('approval_status', 'Status', 'trim|required');
        if ($this->input->post('approval_status') == 2) {
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        }
        $this->form_validation->set_rules('salaryOrderID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {
            echo json_encode($this->Employee_model->save_fixed_element_declaration_approval());
        }
    }

    public function save_empMonthlyAdditionOT()
    {
        $this->form_validation->set_rules('dateDesc', 'Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $date_format_policy = date_format_policy();
            $updateCode = $this->input->post('updateCode');
            $empArr = $this->input->post('empHiddenID');
            $dtDsc = $this->input->post('dateDesc');
            $dateDesc = input_format_date($dtDsc, $date_format_policy);
            $payYear = date('Y', strtotime($dateDesc));
            $payMonth = date('m', strtotime($dateDesc));

            $masterData = $this->Employee_model->edit_OT_monthAddition();
            if ($masterData['isProcessed'] == 1) {
                exit(json_encode(['e', $updateCode . ' is already processed you can not make changes on this.']));
            }

            if ($masterData['confirmedYN'] == 1) {
                exit(json_encode(['e', $updateCode . ' is already confirmed you can not make changes on this.']));
            }

            $empArr = join(',', $empArr);
            $isPayrollProcessed = isPayrollProcessedForEmpGroup($empArr, $payYear, $payMonth, 'N');

            if (empty($isPayrollProcessed)) {
                echo json_encode($this->Employee_model->save_empMonthlyAdditionOT());
            } else {
                $employeesStr = implode('<br/>', array_column($isPayrollProcessed, 'empData'));
                $yearMonth = date('Y - M', strtotime($dtDsc));
                echo json_encode(array('e', 'Payroll already processed on selected <br/> month (' . $yearMonth . ') for following employees <br/>' . $employeesStr));
            }
        }
    }


    function loadOTDetail_table()
    {
        $masterID = $this->input->post('masterID');
        $companyID = current_companyID();
        $data['masterData'] = $this->Employee_model->edit_OT_monthAddition($masterID);


        $details = $this->db->query("SELECT detailTB.*, EIdNo, ECode, Ename2 AS empName
                                 FROM srp_erp_ot_monthlyadditiondetail AS detailTB
                                 JOIN srp_employeesdetails AS empTB ON detailTB.empID=empTB.EIdNo  AND Erp_companyID={$companyID}
                                 WHERE monthlyAdditionsMasterID = {$masterID} AND detailTB.companyID = {$companyID} ORDER BY ECode ASC")->result_array();
        $data['details'] = $details;
        $this->load->view('system/hrm/OverTimeManagementSalamAir/monthly-add-table-view', $data);

    }

    function removeAllEmp_OT()
    {
        echo json_encode($this->Employee_model->removeAllEmp_OT());
    }

    function remove_emp_OT()
    {
        echo json_encode($this->Employee_model->remove_emp_OT());
    }

    function delete_fixedElement_declaration_detail()
    {
        echo json_encode($this->Employee_model->delete_fixedElement_declaration_detail());
    }

    function calculateOTBlockHours()
    {
        $companyID = current_companyID();
        $empID = $this->input->post('empID');
        $dPlaces = $this->input->post('dPlaces');
        $h = $this->input->post('hours');
        $m = $this->input->post('minutes');
        $calHours = $totHours = ($h + ($m / 60));
        $totAmount = 0;

        //SELECT IF(startHour=0, 1, startHour) AS startHour, EndHour, slabDet.hourlyRate, otSlabsMasterID
        $slabDet = $this->db->query("SELECT startHour, EndHour, slabDet.hourlyRate, otSlabsMasterID
                                     FROM srp_erp_ot_groupemployees AS empTB
                                     JOIN srp_erp_ot_groupdetail AS grpDet ON grpDet.otGroupID = empTB.otGroupID  AND systemInputID=4
                                     JOIN srp_erp_ot_slabdetail AS slabDet ON slabDet.otSlabsMasterID = grpDet.slabMasterID
                                     AND slabDet.companyID={$companyID}
                                     WHERE empTB.companyID={$companyID} AND empID={$empID} ORDER BY startHour")->result_array();


        if (!empty($slabDet)) {
            foreach ($slabDet as $key => $row) {
                $starH = $row['startHour'];
                $endH = $row['EndHour'];
                $rate = $row['hourlyRate'];

                if ($totHours >= $starH) {
                    //$slabRangeInHours = ($endH - $starH) + 1;
                    $slabRangeInHours = ($endH - $starH);

                    if ($calHours > $slabRangeInHours) {
                        $slabHours = $slabRangeInHours;
                    } else {
                        $slabHours = $calHours;
                    }

                    $totAmount += $slabHours * $rate;
                    $calHours -= $slabHours;
                }

            }
            echo json_encode(['s', number_format($totAmount, $dPlaces, '.', ''), $row['otSlabsMasterID']]);
            //echo json_encode(['s', $totAmount,  $row['otSlabsMasterID']]);
        } else {
            echo json_encode(['e', 'There is not a valid slab detail for this employee']);
        }

    }

    function delete_OT_monthAddition()
    {
        $delID = $this->input->post('delID');

        $isConfirmed = $this->Employee_model->edit_OT_monthAddition($delID);
        $deleteArray = ['monthlyAdditionsMasterID' => $delID, 'companyID' => current_companyID()];

        if ($isConfirmed['isProcessed'] == 1) {
            echo json_encode(['e', 'This document is already processed, You can not delete this.']);
        } else if ($isConfirmed['confirmedYN'] == 1) {
            echo json_encode(['e', 'This document is already confirmed, You can not delete this.']);
        } else {
            $this->db->trans_start();
            $this->db->delete('srp_erp_ot_monthlyadditionsmaster', $deleteArray);
            $this->db->delete('srp_erp_ot_monthlyadditiondetail', $deleteArray);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                echo json_encode(['s', 'Deleted successfully']);
            } else {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Failed to delete record']);
            }
        }

    }

    function referBack_OT_monthAddition()
    {
        $referID = $this->input->post('referID');

        $isConfirmed = $this->Employee_model->edit_OT_monthAddition($referID);
        $where = ['monthlyAdditionsMasterID' => $referID, 'companyID' => current_companyID()];

        if ($isConfirmed['isProcessed'] == 1) {
            echo json_encode(['e', 'This document is already processed, You can not refer back this.']);
        } else {
            $this->db->trans_start();
            $data_master['confirmedYN'] = 2;
            $data_master['confirmedByEmpID'] = '';
            $data_master['confirmedByName'] = '';
            $data_master['confirmedDate'] = '';
            $this->db->where($where)->update('srp_erp_ot_monthlyadditionsmaster', $data_master);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                echo json_encode(['s', 'Refer backed successfully']);
            } else {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Failed to delete record']);
            }
        }
    }

    function update_userName()
    {

        $this->form_validation->set_rules('UserName', 'Username', 'trim|required|valid_email');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->update_userName());
        }
    }

    function fetch_family_details()
    {
        $empID = $this->input->post('empID');
        $data['empArray'] = $this->Employee_model->fetch_family_details($empID, 1);
        $this->load->view('system/hrm/ajax/ajax-employee_profile_load_info', $data);
    }

    function saveFamilyDetails()
    {
        $this->form_validation->set_rules('employeeID', 'ID', 'trim|required');
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('nationality', 'Nationality', 'required|numeric');
        $this->form_validation->set_rules('relationshipType', 'Relationship', 'required|numeric');
        $this->form_validation->set_rules('DOB', 'Date of Birth', 'trim|required');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo $this->Employee_model->insert_familyDetails();
        }
    }

    function ajax_update_familydetails()
    {
        $result = $this->Employee_model->xeditable_update('srp_erp_family_details', 'empfamilydetailsID');
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'updated'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'updated Fail'));
        }
    }

    function delete_familydetail()
    {
        echo json_encode($this->Employee_model->delete_familydetail());
    }


    function familyimage_upload()
    {

        $fileName = $this->input->post('empfamilydetailsID') . '_FD_' . time();
        $config['upload_path'] = realpath(APPPATH . '../images/family_images');
        $config['allowed_types'] = 'gif|jpg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|txt|rtf|msg';
        $config['max_size'] = '200000';
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload("document_file")) {
            echo json_encode(array('e', 'Upload failed ' . $this->upload->display_errors()));
        } else {
            $data1 = $this->upload->data();
            $fileName = $this->input->post('empfamilydetailsID') . '_FD_' . time() . $data1["file_ext"];

            $upData = array(
                'image' => $fileName,
            );
            $result = $this->db->where('empfamilydetailsID', $this->input->post('empfamilydetailsID'))->update('srp_erp_family_details', $upData);

            if ($result) {
                echo json_encode(array('s', 'Image uploaded successfully'));
            }
        }
    }


    function fetch_family_attachment_details()
    {
        $path = base_url('images/family_attachment/');
        $companyid = $this->common_data['company_data']['company_id'];
        $empFamilyDetailsID = $this->input->post('empFamilyDetailsID');
        $where = "companyID = " . $companyid . " And empFamilyDetailsID = " . $empFamilyDetailsID;
        $this->datatables->select("attachmentID,documentID,empID,empFamilyDetailsID,attachmentDescription,concat('$path',myFileName) as myFileName",false);
        $this->datatables->from('srp_erp_familydetailsattachments');
        $this->datatables->where($where);
        $this->datatables->add_column('document', '$1', 'getDocumentfamilyAttachment(documentID)');
        $this->datatables->add_column('edit', '<a onclick="delete_family_attachment($1,$2)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>', 'attachmentID,empFamilyDetailsID');
        $this->datatables->add_column('desc', '$1', 'generate_encrypt_link(myFileName, attachmentDescription)');
        echo $this->datatables->generate();
    }


    function familyattachment_uplode()
    {

        $this->form_validation->set_rules('attachmentDescription', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            $fileName = $this->input->post('empfamilydetailsAttachID') . '_FDA_' . time();
            $config['upload_path'] = realpath(APPPATH . '../images/family_attachment');
            $config['allowed_types'] = 'gif|jpg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|txt|rtf|msg';
            $config['max_size'] = '2000000';
            $config['file_name'] = $fileName;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload("document_file")) {
                echo json_encode(array('e', 'Upload failed ' . $this->upload->display_errors()));
            } else {
                $data1 = $this->upload->data();
                $fileName = $this->input->post('empfamilydetailsAttachID') . '_FDA_' . time() . $data1["file_ext"];

                $data = array(
                    "documentID" => $this->input->post('documentID'),
                    "empID" => $this->input->post('empIDFamilyAttach'),
                    "empFamilyDetailsID" => $this->input->post('empfamilydetailsAttachID'),
                    "attachmentDescription" => $this->input->post('attachmentDescription'),
                    "companyID" => current_companyID(),
                    "myFileName" => $fileName
                );

                $result = $this->db->insert('srp_erp_familydetailsattachments', $data);

                if ($result) {
                    echo json_encode(array('s', 'File uploaded successfully'));
                }
            }
        }
    }

    function delete_family_attachment()
    {
        echo json_encode($this->Employee_model->delete_family_attachment());
    }

    public function fetch_leave_conformation()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN'));
        $convertFormat = convert_date_format_sql();
        $empID = current_userID();

        /*$this->datatables->select("leaveMasterID,documentCode,ECode,Ename2 as empName,confirmedYN,approvedYN,confirmedByEmpID,srp_erp_leavemaster.empID");
        $this->datatables->join('srp_erp_employeemanagers ', 'srp_erp_leavemaster.empID = srp_erp_employeemanagers.empID');
        $this->datatables->join('srp_employeesdetails ', 'srp_employeesdetails.EIdNo = srp_erp_leavemaster.empID');
        $this->datatables->from('srp_erp_leavemaster');
        $this->datatables->where('srp_erp_leavemaster.companyID', $companyID);
        $this->datatables->where('srp_erp_leavemaster.confirmedYN', 1);
        $this->datatables->where('srp_erp_leavemaster.approvedYN', $approvedYN);
        $this->datatables->where('srp_erp_employeemanagers.managerID', $empID);
        $this->datatables->where('srp_erp_employeemanagers.active', 1);
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"LA",leaveMasterID)');
        $this->datatables->add_column('edit', '$1', 'load_LA_approval_action(leaveMasterID,confirmedYN,approvedYN,confirmedByEmpID)');
        echo $this->datatables->generate();

        die();*/

        if($approvedYN == 1){
            $this->datatables->select("leaveMasterID,lMaster.documentCode AS documentCode,CONCAT(ECode, ' - ', Ename2) AS empName,confirmedYN,appTB.approvedYN AS approvedYN,
                    confirmedByEmpID,lMaster.empID, approvalLevelID AS currentLevelNo, DATE_FORMAT(startDate,'{$convertFormat}') AS startDate, DATE_FORMAT(endDate,'{$convertFormat}') AS endDate");
            $this->datatables->from('srp_erp_leavemaster AS lMaster');
            $this->datatables->join('srp_erp_documentapproved AS appTB ', 'lMaster.leaveMasterID = appTB.documentSystemCode');
            $this->datatables->join('srp_employeesdetails ', 'srp_employeesdetails.EIdNo = lMaster.empID');
            $this->datatables->where('lMaster.companyID', $companyID);
            $this->datatables->where('appTB.companyID', $companyID);
            $this->datatables->where('lMaster.approvedYN', $approvedYN);
            $this->datatables->where('appTB.approvedEmpID', current_userID());
            $this->datatables->where('appTB.documentID', 'LA');
            $this->datatables->where('appTB.isCancel !=1');
            $this->datatables->where('(lMaster.requestForCancelYN = 0 OR lMaster.requestForCancelYN IS NULL )');
            $this->datatables->add_column('levelNo', '<center>Level $1</center>', 'currentLevelNo');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"LA",leaveMasterID)');
            $this->datatables->add_column('edit', '$1', 'load_LA_approval_action(leaveMasterID,confirmedYN,approvedYN,confirmedByEmpID,currentLevelNo)');
            echo $this->datatables->generate();
        }
        else{

            $setupData = getLeaveApprovalSetup();
            $approvalLevel = $setupData['approvalLevel'];
            $approvalSetup = $setupData['approvalSetup'];
            $approvalEmp_arr = $setupData['approvalEmp'];

            $x = 0;
            $str = 'CASE';
            while($x < $approvalLevel) {
                $level = $x + 1;
                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $level);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';
                if($approvalType == 3){
                    /*$hrManagerID = (!empty($arr[0])) ? $arr[0]['empID'] : '0';
                    $str .= ' WHEN( currentLevelNo = '.$level.' ) THEN IF( \''.$empID.'\' = '.$hrManagerID.', 1, 0 ) ';*/

                    $hrManagerID = (array_key_exists($level, $approvalEmp_arr)) ? $approvalEmp_arr[$level] : [];
                    $hrManagerID = array_column($hrManagerID, 'empID');

                    if(!empty($hrManagerID)){
                        $str .= ' WHEN( currentLevelNo = '.$level.' ) THEN IF( ';
                        foreach($hrManagerID as $key=>$hrManagerRow){
                            $str .= ($key > 0)? ' OR': '';
                            $str .= ' ( \''.$empID.'\' = '.$hrManagerRow.')';
                        }
                        $str .= ' , 1, 0 ) ';
                    }
                }
                else{
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    $str .= ' WHEN( currentLevelNo = '.$level.' ) THEN IF( '.$managerType.' = '.$empID.', 1, 0 ) ';
                }


                $x++;
            }
            $str .= 'END AS isInApproval';


            $this->datatables->select("leaveMasterID, documentCode, CONCAT(ECode, ' - ', empName) AS empName, approvedYN, empID, currentLevelNo, repManager, 
                      DATE_FORMAT(startDate,'{$convertFormat}') AS startDate, DATE_FORMAT(endDate,'{$convertFormat}') AS endDate");
            $this->datatables->from("( SELECT *, {$str} FROM (
                                            SELECT leaveMasterID, documentCode, ECode, Ename2 AS empName, approvedYN, lMaster.empID, currentLevelNo,
                                            repManager, coveringEmpID AS coveringEmp, startDate, endDate
                                            FROM srp_erp_leavemaster AS lMaster
                                            JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = lMaster.empID
                                            LEFT JOIN (
                                                SELECT empID, managerID AS repManager
                                                FROM srp_erp_employeemanagers WHERE active = 1 AND companyID={$companyID}
                                            ) AS repoManagerTB ON lMaster.empID = repoManagerTB.empID
                                            WHERE lMaster.companyID = '{$companyID}' AND lMaster.confirmedYN = 1 AND
                                            lMaster.approvedYN = '0'
                                        ) AS leaveData
                                        LEFT JOIN (
                                            SELECT managerID AS topManager, empID AS topEmpID
                                            FROM srp_erp_employeemanagers WHERE companyID={$companyID} AND active = 1
                                        ) AS topManagerTB ON leaveData.repManager = topManagerTB.topEmpID
                                       ) AS t1");
            $this->datatables->where('t1.isInApproval', 1);
            $this->datatables->add_column('levelNo', '<center>Level $1</center>', 'currentLevelNo');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"LA",leaveMasterID)');
            $this->datatables->add_column('edit', '$1', 'load_LA_approval_action(leaveMasterID,confirmedYN,approvedYN,confirmedByEmpID, currentLevelNo)');
            echo $this->datatables->generate();
        }
    }

    public function fetch_leave_cancellation_approval()
    {
        $companyID = current_companyID();
        $approvedYN = trim($this->input->post('approvedYN'));
        $convertFormat = convert_date_format_sql();
        $empID = current_userID();

        if($approvedYN == 1){
            $this->datatables->select("leaveMasterID,lMaster.documentCode AS documentCode,ECode,CONCAT(ECode, ' - ', Ename2) AS empName,confirmedYN,appTB.approvedYN AS approvedYN,
                    confirmedByEmpID,lMaster.empID, approvalLevelID AS currentLevelNo, DATE_FORMAT(startDate,'{$convertFormat}') AS startDate, DATE_FORMAT(endDate,'{$convertFormat}') AS endDate");
            $this->datatables->from('srp_erp_leavemaster AS lMaster');
            $this->datatables->join('srp_erp_documentapproved AS appTB ', 'lMaster.leaveMasterID = appTB.documentSystemCode');
            $this->datatables->join('srp_employeesdetails ', 'srp_employeesdetails.EIdNo = lMaster.empID');
            $this->datatables->where('lMaster.companyID', $companyID);
            $this->datatables->where('appTB.companyID', $companyID);
            $this->datatables->where('lMaster.approvedYN', $approvedYN);
            $this->datatables->where('appTB.approvedEmpID', current_userID());
            $this->datatables->where('appTB.documentID', 'LA');
            $this->datatables->where('lMaster.cancelledYN', '1');
            $this->datatables->where('appTB.isCancel', '1');
            $this->datatables->add_column('levelNo', '<center>Level $1</center>', 'currentLevelNo');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"LA",leaveMasterID)');
            $this->datatables->add_column('edit', '$1', 'load_LA_approval_action(leaveMasterID,confirmedYN,approvedYN,confirmedByEmpID,currentLevelNo)');
            echo $this->datatables->generate();
        }
        else{

            $setupData = getLeaveApprovalSetup();
            $approvalLevel = $setupData['approvalLevel'];
            $approvalSetup = $setupData['approvalSetup'];
            $approvalEmp_arr = $setupData['approvalEmp'];

            $x = 0;
            $str = 'CASE';
            while($x < $approvalLevel) {
                $level = $x + 1;
                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $level);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';
                if($approvalType == 3){
                    /*$hrManagerID = (!empty($arr[0])) ? $arr[0]['empID'] : '0';
                    $str .= ' WHEN( currentLevelNo = '.$level.' ) THEN IF( \''.$empID.'\' = '.$hrManagerID.', 1, 0 ) ';*/

                    $hrManagerID = (array_key_exists($level, $approvalEmp_arr)) ? $approvalEmp_arr[$level] : [];
                    $hrManagerID = array_column($hrManagerID, 'empID');

                    if(!empty($hrManagerID)){
                        $str .= ' WHEN( currentLevelNo = '.$level.' ) THEN IF( ';
                        foreach($hrManagerID as $key=>$hrManagerRow){
                            $str .= ($key > 0)? ' OR': '';
                            $str .= ' ( \''.$empID.'\' = '.$hrManagerRow.')';
                        }
                        $str .= ' , 1, 0 ) ';
                    }
                }
                else{
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    $str .= ' WHEN( currentLevelNo = '.$level.' ) THEN IF( '.$managerType.' = '.$empID.', 1, 0 ) ';
                }


                $x++;
            }
            $str .= 'END AS isInApproval';


            $this->datatables->select("leaveMasterID, documentCode, ECode, CONCAT(ECode, ' - ', Ename2) AS empName, 0 AS approvedYN, empID, currentLevelNo, repManager,
                    DATE_FORMAT(startDate,'{$convertFormat}') AS startDate, DATE_FORMAT(endDate,'{$convertFormat}') AS endDate");
            $this->datatables->from("( SELECT *, {$str} FROM (
                                            SELECT leaveMasterID, documentCode, ECode, Ename2, approvedYN, lMaster.empID, currentLevelNo,
                                            repManager, coveringEmpID AS coveringEmp, startDate, endDate
                                            FROM srp_erp_leavemaster AS lMaster
                                            JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = lMaster.empID
                                            LEFT JOIN (
                                                SELECT empID, managerID AS repManager
                                                FROM srp_erp_employeemanagers WHERE active = 1 AND companyID={$companyID}
                                            ) AS repoManagerTB ON lMaster.empID = repoManagerTB.empID
                                            WHERE lMaster.companyID = '{$companyID}' AND lMaster.confirmedYN = 1 AND
                                            lMaster.approvedYN = '1' AND lMaster.cancelledYN = '0' AND requestForCancelYN = 1
                                        ) AS leaveData
                                        LEFT JOIN (
                                            SELECT managerID AS topManager, empID AS topEmpID
                                            FROM srp_erp_employeemanagers WHERE companyID={$companyID} AND active = 1
                                        ) AS topManagerTB ON leaveData.repManager = topManagerTB.topEmpID
                                       ) AS t1");
            $this->datatables->where('t1.isInApproval', 1);
            $this->datatables->add_column('levelNo', '<center>Level $1</center>', 'currentLevelNo');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"LA",leaveMasterID)');
            $this->datatables->add_column('edit', '$1', 'load_LA_approval_action(leaveMasterID,confirmedYN,approvedYN,confirmedByEmpID, currentLevelNo,1)');
            echo $this->datatables->generate();
        }
    }

    public function leaveApproval()
    {
        $this->form_validation->set_rules('status', 'Leave Application Status', 'trim|required');
        if ($this->input->post('status') == 2) {
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        }
        $this->form_validation->set_rules('hiddenLeaveID', 'Leave Application ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if ($this->input->post('isFromCancelYN') == 1) {
                die( json_encode($this->Employee_model->leave_cancellation_approval()) );
            }
            echo json_encode($this->Employee_model->save_leaveApproval());
        }
    }

    /** SSO slab master**/
    public function save_ssoSlabMaster()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $companyID = current_companyID();
            $description = trim($this->input->post('description'));

            $isExist = $this->db->query("SELECT description FROM srp_erp_ssoslabmaster WHERE companyID={$companyID}
                                         AND description='{$description}'")->row('description');

            if (empty($isExist)) {
                $data = array(
                    'description' => $description,
                    'companyID' => $companyID,
                    'createdPCID' => current_pc(),
                    'createdUserGroup' => current_user_group(),
                    'createdUserID' => current_userID(),
                    'createdUserName' => current_employee(),
                    'createdDateTime' => current_date()
                );

                $this->db->insert('srp_erp_ssoslabmaster', $data);
                if ($this->db->affected_rows() > 0) {
                    $insertID = $this->db->insert_id();
                    echo json_encode(['s', 'Slab master successfully created', $insertID]);
                } else {
                    echo json_encode(['e', 'Error in slab master create process']);
                }
            } else {
                echo json_encode(['e', 'This description is already exist.']);
            }
        }
    }

    function fetch_sso_slab_master()
    {
        $this->datatables->select("ssoSlabMasterID,Description");
        $this->datatables->from('srp_erp_ssoslabmaster');
        $this->datatables->where('companyID', current_companyID());
        $this->datatables->add_column('edit', '$1', 'sso_slab_action(ssoSlabMasterID,Description)');
        echo $this->datatables->generate();
    }

    function save_sso_slabs_detail()
    {
        $this->form_validation->set_rules('start_amount', 'Start amount', 'trim|required');
        $this->form_validation->set_rules('end_amount', 'End amount', 'trim|required');
        $this->form_validation->set_rules('masterID', 'Master ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $companyID = current_companyID();
            $masterID = $this->input->post('masterID');
            $start_amount = str_replace(',', '', $this->input->post('start_amount'));
            $end_amount = $this->input->post('end_amount');

            if ($start_amount >= $end_amount) {
                die(json_encode(['e', 'End range amount should be greater than start range amount.<br/>Please refresh the page and try again.']));
            }

            $lastRange = $this->db->query("SELECT * FROM srp_erp_ssoslabdetails WHERE companyID={$companyID}
                                         AND ssoSlabMasterID={$masterID} ORDER BY endRangeAmount DESC LIMIT 1")->row_array();

            if (!empty($lastRange)) {
                $endRangeAmount = $lastRange['endRangeAmount'];

                if ($start_amount != ($endRangeAmount + 1)) {
                    die(json_encode(['e', 'Start range amount should be greater than last inserted end range.
                                            <br/>Please refresh the page and try again.']));
                }
            }

            $data = array(
                'ssoSlabMasterID' => $masterID,
                'startRangeAmount' => $start_amount,
                'endRangeAmount' => $end_amount,
                'companyID' => $companyID,
                'createdPCID' => current_pc(),
                'createdUserGroup' => current_user_group(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_ssoslabdetails', $data);
            if ($this->db->affected_rows() > 0) {
                echo json_encode(['s', 'Slab detail inserted successfully.']);
            } else {
                echo json_encode(['e', 'Error in slab detail insert process']);
            }
        }
    }

    function delete_ssoSlabDetail()
    {
        $this->form_validation->set_rules('masterID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('detailID', 'Slab Detail ID', 'trim|required');
        $this->form_validation->set_rules('strRange', 'Start Range Amount', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $companyID = current_companyID();
            $masterID = $this->input->post('masterID');
            $detailID = $this->input->post('detailID');
            $strRange = $this->input->post('strRange');

            $where = [
                'companyID' => $companyID,
                'ssoSlabMasterID' => $masterID,
            ];

            $this->db->select("MAX(startRangeAmount) AS lastStartRange");
            $this->db->from("srp_erp_ssoslabdetails");
            $this->db->where($where);
            $lastStartRange = $this->db->get()->row('lastStartRange');

            if ($lastStartRange != $strRange) {
                die(json_encode(['e', 'First delete slab ranges greater than this slab range.']));
            }

            $where['ssoSlabDetailID'] = $detailID;

            $this->db->where($where)->delete('srp_erp_ssoslabdetails');

            if ($this->db->affected_rows() > 0) {
                echo json_encode(['s', 'Slab detailed deleted successfully.']);
            } else {
                echo json_encode(['e', 'Error in slab detail delete process']);
            }
        }
    }

    function save_ssoSlabsFormula()
    {
        $detailID = $this->input->post('payGroupID');
        $formulaStr = $this->input->post('formulaString');
        $salaryCategories = $this->input->post('salaryCategoryContainer');
        $salaryCategories = (trim($salaryCategories) == '') ? null : $salaryCategories;
        $ssoCategories = $this->input->post('SSOContainer');
        $ssoCategories = (trim($ssoCategories) == '') ? null : $ssoCategories;
        $payGroupCategories = $this->input->post('payGroupContainer');
        $payGroupCategories = (trim($payGroupCategories) == '') ? null : $payGroupCategories;
        $companyID = current_companyID();

        if ($payGroupCategories != null) {
            /*************************************************************************************************
             * Is the formula contain pay group check, than the pay group is only contains salary categories
             *************************************************************************************************/
            $payGroupData = $this->db->query("SELECT masterTB.description FROM srp_erp_paygroupmaster AS masterTB
                                              JOIN srp_erp_paygroupformula AS formula ON formula.payGroupID=masterTB.payGroupID
                                              WHERE masterTB.companyID = '{$companyID}' AND formula.payGroupID IN ({$payGroupCategories})
                                              AND (ssoCategories IS NOT NULL OR payGroupCategories IS NOT NULL )")->result_array();

            if (!empty($payGroupData)) {
                $description = implode('<br/>-', array_column($payGroupData, 'description'));
                die(json_encode(['e', 'Following pay group/groups should only contain salary categories<br/>-' . $description]));
            }
        }

        $where = [
            'companyID' => $companyID,
            'ssoSlabDetailID' => $detailID,
        ];

        $data = [
            'formulaString' => $formulaStr,
            'salaryCategories' => $salaryCategories,
            'ssoCategories' => $ssoCategories,
            'payGroupCategories' => $payGroupCategories,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => current_date()
        ];

        $this->db->trans_start();
        $this->db->where($where)->update('srp_erp_ssoslabdetails', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo json_encode(['s', 'Record inserted successfully']);
        } else {
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in process']);
        }

    }

    function ajax_update_ssoSlabDescription()
    {
        $companyID = current_companyID();
        $masterID = $this->input->get('masterID');
        $description = trim($this->input->post('value'));

        if ($description == '') {
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Description is required.');
        }

        $where = [
            'companyID' => $companyID,
            'ssoSlabMasterID' => $masterID,
        ];

        $isExist = $this->db->query("SELECT ssoSlabMasterID FROM srp_erp_ssoslabmaster WHERE companyID={$companyID}
                                         AND description='{$description}'")->row('ssoSlabMasterID');


        if (!empty($isExist) && $isExist != $masterID) {
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('This description is already exist.');
        }

        $data = [
            'description' => $description,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => current_date()
        ];

        $this->db->trans_start();
        $this->db->where($where)->update('srp_erp_ssoslabmaster', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo json_encode(['s', 'Description updated successfully']);
        } else {
            $this->db->trans_rollback();
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Error in description Update process');
        }
    }

    function save_salary_category()
    {
        $this->form_validation->set_rules('nopaySystemID', 'Description ', 'trim|required');
        $this->form_validation->set_rules('salaryCategoryID', 'Salary Category', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->save_salary_category());
        }
    }

    function edit_salary_category()
    {
        $this->form_validation->set_rules('noPaySystemIDHidden', 'Description ', 'trim|required');
        $this->form_validation->set_rules('salaryCategoryID', 'Salary Category', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->edit_salary_category());
        }
    }

    function loadLeaveTypeDropDown()
    {

        $empID = $this->input->post('empID');
        $confirmedYN = trim($this->input->post('confirmedYN'));

        if ($empID != '') {
            $companyID = current_companyID();
            $output = $this->db->query("SELECT policyMasterID, lType.leaveTypeID, lType.description, leaveGroupDetailID, isSickLeave,
                                        groupDet.leaveGroupID, noOfDays, isAllowminus, isCalenderDays, noOfHours, noOfHourscompleted
                                        FROM srp_employeesdetails AS empTB
                                        LEFT JOIN srp_erp_leavegroupdetails AS groupDet ON empTB.leaveGroupID=groupDet.leaveGroupID
                                        LEFT JOIN  srp_erp_leavetype AS lType ON groupDet.leaveTypeID=lType.leaveTypeID
                                        WHERE EIdNo='{$empID}' AND empTB.Erp_companyID='{$companyID}'
                                        AND lType.companyID='{$companyID}' AND typeConfirmed=1 ORDER BY sortOrder")->result_array();
        }
        $html = '<select name="leaveTypeID" class="form-control frm_input" onchange="getemplate(this)" id="leaveTypeID">';
        $html .= '<option value=""></option>';
        if (!empty($output)) {
            $isBasedOnSortOrder = getPolicyValues('SL', 'All');
            $isSickLeavePulled = 0;
            foreach ($output as $value) {
                $policyMasterID = $value['policyMasterID'];
                $leaveTypeID = $value['leaveTypeID'];
                $isSickLeave = $value['isSickLeave'];
                $leaveGroupID = $value['leaveGroupID'];
                $isValid = 'Y';

                /************************************************************************************************
                 * If document confirmed then no need to validate sick leave short order
                 ************************************************************************************************/
                if($confirmedYN != 1){
                    /*** Validate is sick leave based on sort order***/
                    if($isBasedOnSortOrder == 1 && $isSickLeave == 1){
                        if($isSickLeave == 1 && $isSickLeavePulled == 0){
                            $leaveData = $this->Employee_model->employeeLeaveSummery($empID, $leaveTypeID, $policyMasterID);
                            if($leaveData['balance'] == 0){
                                $isValid = 'N';
                            }else{
                                $isSickLeavePulled = 1;
                            }
                        }
                        else{
                            $isValid = 'N';
                        }
                    }
                }

                if($isValid == 'Y'){
                    $html .= '<option data-policyMasterID="' . $policyMasterID . '" data-leaveTypeID="' . $leaveTypeID . '" data-leaveGroupID="' . $leaveGroupID . '"';
                    $html .= ' data-isAllowminus="' . $value['isAllowminus'] . '" data-isCalenderDays="' . $value['isCalenderDays'].'"';
                    $html .= ' data-isAllowminus="' . $value['isAllowminus'] . '" data-isCalenderDays="' . $value['isCalenderDays'].'"';
                    $html .= ' value="' . $value['leaveTypeID'] . '">' . $value['description']. '</option>';
                }
            }

        }
        $html .= '</select>';

        echo $html;


    }

    function save_company_active()
    {
        echo json_encode($this->Employee_model->save_company_active());
    }

    function get_noPaySalaryCategories()
    {
        $type = $this->input->post('payType');
        $selectedID = $this->input->post('selectedID');
        $data = system_salary_cat_drop_nopay($type);

        $response = '<option value="">Select Description</option>';

        if (!empty($data)) {
            foreach ($data as $row) {
                $selected = ($row['salaryCategoryID'] == $selectedID) ? 'selected' : '';
                $response .= '<option value="' . $row['salaryCategoryID'] . '"  ' . $selected . '>' . $row['salaryDescription'] . '</option>';
            }
        }

        echo $response;
    }

    function save_user_change_password()
    {
        echo json_encode($this->Employee_model->save_user_change_password());
    }

    function update_leave_adjustmentcomment()
    {
        $masterID = $this->input->post('masterID');
        $value = $this->input->post('value');
        $empID = $this->input->post('empID');
        $this->db->update('srp_erp_leaveaccrualdetail', array('comment' => $value), array('empID' => $empID, 'leaveaccrualMasterID' => $masterID));
        echo json_encode(array('s', 'success'));
    }

    function leave_adjustment_employees_drop()
    {
        $companyID = current_companyID();
        $leaveGroupID = $this->input->post('leaveGroupID');
        $masterID = $this->input->post('masterID');
        $policyMasterID = $this->input->post('policyMasterID');
        $segmentID = $this->input->post('segmentID');
        $employee = array();
        if ($segmentID != '') {
            $commaList = implode(', ', $segmentID);


            $employee = $this->db->query("SELECT srp_employeesdetails.* FROM srp_employeesdetails 
                          LEFT JOIN (SELECT * FROM srp_erp_leaveaccrualdetail WHERE leaveaccrualMasterID = {$masterID} 
                          AND leaveGroupID = $leaveGroupID GROUP BY empID)  t on t.empID=srp_employeesdetails.EIdNo 
                          INNER JOIN srp_erp_leavegroupdetails on srp_erp_leavegroupdetails.leaveGroupID=$leaveGroupID 
                          AND srp_erp_leavegroupdetails.policyMasterID=$policyMasterID 
                          WHERE Erp_companyID = {$companyID} AND srp_employeesdetails.isDischarged = 0 AND isSystemAdmin=0 
                          AND srp_employeesdetails.leaveGroupID = $leaveGroupID AND leaveaccrualDetailID is NULL 
                          AND srp_employeesdetails.segmentID IN ($commaList) AND empConfirmedYN=1 GROUP BY EIdNo")->result_array();
        }

        $html = "<select name='empID[]' class='form-control frm_input empID ' multiple id='empID'>";

        if ($employee) {
            foreach ($employee as $emp) {
                $html .= '<option value="' . $emp['EIdNo'] . '" >' . $emp['ECode'] . ' | ' . $emp['Ename2'] . '</option>';
            }
        }
        $html .= '</select>';

        echo $html;

    }

    function save_leave_adjustmentDetail()
    {
        $empID = $this->input->post('empID');
        if (empty($empID)) {
            $this->session->set_flashdata('e', 'Please select a employee.');
            echo json_encode(array('error' => 'e', 'message' => 'Please select a employee'));
            exit;
        }
        $masterID = $this->input->post('masterID');
        $leaveGroupID = $this->input->post('leaveGroupID');
        $policyMasterID = $this->input->post('policyMasterID');
        $companyID = current_companyID();
        $commaList = implode(', ', $empID);
        $q2 = "SELECT DateAssumed, CONCAT(EIdNo, '-', srp_erp_leavetype.leaveTypeID) AS leaveTypeKey, EIdNo, srp_employeesdetails.leaveGroupID, srp_erp_leavegroupdetails.*, policyID FROM `srp_employeesdetails` INNER JOIN `srp_erp_leavegroupdetails` ON srp_erp_leavegroupdetails.leaveGroupID = srp_employeesdetails.leaveGroupID  AND policyMasterID IN($policyMasterID) INNER JOIN `srp_erp_leavetype` ON srp_erp_leavegroupdetails.leaveTypeID = srp_erp_leavetype.leaveTypeID WHERE srp_employeesdetails.isDischarged=0 AND Erp_companyID = {$companyID} AND srp_employeesdetails.EIdNo IN($commaList) AND srp_employeesdetails.leaveGroupID = {$leaveGroupID}  GROUP BY EIdNo , leaveTypeID";


        $result = $this->db->query($q2)->result_array();

        /*  $updateArr      = array();*/
        /*   $insert_Arr     = array();*/
        $detail = array();
        if ($result) {
            foreach ($result as $val) {
                $daysEntitled = 0;
                $hoursEntitled = 0;
                $datas = array('leaveaccrualMasterID' => $masterID, 'empID' => $val['EIdNo'], 'leaveGroupID' => $leaveGroupID, 'leaveType' => $val['leaveTypeID'], 'daysEntitled' => $daysEntitled, 'hoursEntitled' => $hoursEntitled, 'description' => 'Leave Adjustment ' . date('Y/m'), 'createDate' => date('Y-m-d H:i:s'), 'createdUserGroup' => current_user_group(), 'createdPCid' => current_pc());

                /*  array_push($insert_Arr, array('leaveTypeID'     => $val['leaveTypeID'], 'empID' => $val['EIdNo'], 'days' => $daysEntitled, 'hourly'          => $hoursEntitled, 'companyID' => current_companyID(), 'companyCode'     => current_companyCode(), 'createdUserGroup' => '', 'createdPCID'     => $this->common_data['current_pc'], 'createdUserID'   => $this->common_data['current_userID'], 'createdDateTime' => current_date(), 'createdUserName' => $this->common_data['current_user'],));*/
                array_push($detail, $datas);
            }

            $this->db->insert_batch('srp_erp_leaveaccrualdetail', $detail);
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('e', 'Failed.');
            echo json_encode(array('error' => 'e', 'message' => 'Failed'));
            exit;
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Leave Accrual inserted successfully.');
            echo json_encode(array('error' => 's', 'leaveGroupID' => $masterID));
            exit;
        }
    }

    function loadLeaveBalance()
    {
        $data['empID'] = current_userID();
        $leavGroup = $this->db->query("SELECT leaveGroupID FROM `srp_employeesdetails` WHERE EIdNo =  {$data['empID']}")->row_array();
        $data['leavGroupID'] = $leavGroup['leaveGroupID'];
        if ($leavGroup['leaveGroupID'] == '') {
            $data['leaveType'] = array();
        } else {
            $data['leaveType'] = $this->db->query("SELECT srp_erp_leavegroupdetails.leaveTypeID,policyMasterID,description FROM `srp_erp_leavegroupdetails` LEFT JOIN `srp_erp_leavetype` ON srp_erp_leavegroupdetails.leaveTypeID = srp_erp_leavetype.leaveTypeID WHERE leaveGroupID = {$data['leavGroupID']}  ORDER BY srp_erp_leavetype.description asc")->result_array();
        }
        //echo '<pre>'; print_r($data['leaveType']); echo '</pre>';
        $this->load->view('system/hrm/ajax/ajax_leave_balance.php', $data);
    }

    function loadLeaveBalanceHistory()
    {
        $leaveTypeID = $this->input->post('leaveTypeID');
        $isFromEmployeeMaster = $this->input->post('isFromEmployeeMaster');

        $userID = ($isFromEmployeeMaster == 'Y')? $this->input->post('empID') :current_userID();
        $isCarryForwardStr = $isCarryForwardStr2 = '';

        $isCarryForward = $this->db->query("SELECT IF(policyMasterID=1, isCarryForward, 2) AS isCarry FROM srp_erp_leavegroupdetails  t1
                JOIN srp_employeesdetails t2 ON t1.leaveGroupID=t2.leaveGroupID
                WHERE leaveTypeID={$leaveTypeID}  AND EIdNo={$userID}")->row('isCarry');
        if($isCarryForward == 0){
            $isCarryForwardStr = " AND `year`='".date('Y')."'";
            $isCarryForwardStr2 = " AND year(startDate) = '".date('Y')."'";
        }

        $data['leave'] = $this->db->query("SELECT IF(policyMasterID = 2, CONCAT(FLOOR(hours / 60), 'h ', ABS(MOD(hours, 60)), 'm')  , days) AS leavedays, IF(policyMasterID = 2, startDate, DATE_FORMAT(startDate,'%Y-%m-%d')) AS startDate, IF(policyMasterID = 2, endDate, DATE_FORMAT(endDate,'%Y-%m-%d')) AS endDate, documentCode, policyMasterID, approvedbyEmpName, approvedDate,comments, case when policyMasterID=2 then 'hour' WHEN policyMasterID=1 then 'Annually' WHEN policyMasterID=3 then 'Monthly' END as policy FROM `srp_erp_leavemaster` WHERE `empID` = $userID AND `approvedYN` = '1' AND leaveTypeID=$leaveTypeID {$isCarryForwardStr2}")->result_array();

        $data['leaveTotal'] = $this->db->query("SELECT if(policyMasterID=2, CONCAT(FLOOR(leavedays / 60), 'h ', ABS(MOD(leavedays, 60)), 'm'),leavedays) as total FROM (SELECT sum(IF(policyMasterID = 2, hours, days)) AS leavedays, policyMasterID FROM `srp_erp_leavemaster` WHERE `empID` = $userID AND `approvedYN` = '1' AND leaveTypeID = $leaveTypeID {$isCarryForwardStr2}) t")->row_array();


        $data['accrued'] = $this->db->query("SELECT if(srp_erp_leaveaccrualmaster.policyMasterID=2,CONCAT(FLOOR(hoursEntitled / 60), 'h ', ABS(MOD(hoursEntitled, 60)), 'm') ,daysEntitled) as entitle ,leaveaccrualMasterCode, DATE_FORMAT(srp_erp_leaveaccrualmaster.createDate,'%Y-%m-%d') AS createDate,srp_erp_leaveaccrualmaster.description FROM `srp_erp_leaveaccrualdetail` INNER JOIN srp_erp_leaveaccrualmaster on srp_erp_leaveaccrualmaster.leaveaccrualMasterID=srp_erp_leaveaccrualdetail.leaveaccrualMasterID AND confirmedYN=1 WHERE empID = $userID AND leaveType=$leaveTypeID {$isCarryForwardStr}")->result_array();

        $data['accruedTotal'] = $this->db->query("select if(policyMasterID=2, CONCAT(FLOOR(entitle / 60), 'h ', ABS(MOD(entitle, 60)), 'm'),entitle) as total FROM (SELECT srp_erp_leaveaccrualmaster.policyMasterID, sum( IF(srp_erp_leaveaccrualmaster.policyMasterID = 2, hoursEntitled, daysEntitled)) AS entitle, leaveaccrualMasterCode, srp_erp_leaveaccrualmaster.createDate, srp_erp_leaveaccrualmaster.description FROM `srp_erp_leaveaccrualdetail` INNER JOIN srp_erp_leaveaccrualmaster ON srp_erp_leaveaccrualmaster.leaveaccrualMasterID = srp_erp_leaveaccrualdetail.leaveaccrualMasterID AND confirmedYN = 1 WHERE empID = $userID AND leaveType = $leaveTypeID {$isCarryForwardStr}) t")->row_array();

        $data['isFromEmployeeMaster'] = $isFromEmployeeMaster;
        $this->load->view('system/hrm/ajax/ajax_leave_history.php', $data);
    }

    function leaveApplicationEmployee()
    {
        $empID = $this->input->post('empID');
        $com = current_companyID();


        if (isset($empID)) {
            $filter = " AND srp_employeesdetails.EIdNo =$empID";
        } else {
            $filter = " AND srp_employeesdetails.isDischarged != 1";

        }

        $qry = "SELECT srp_employeesdetails.EIdNo, srp_employeesdetails.ECode, srp_employeesdetails.EmpSecondaryCode, DesDescription,
              IFNULL(srp_employeesdetails.Ename2, '') AS employee, srp_employeesdetails.leaveGroupID,srp_employeesdetails.DateAssumed,
              IFNULL(DepartmentDes, '') as department, concat(manager.ECode,' | ',manager.Ename2) as manager
              FROM srp_employeesdetails
              INNER JOIN srp_designation on srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
              INNER JOIN srp_erp_leavegroup on srp_employeesdetails.leaveGroupID=srp_erp_leavegroup.leaveGroupID
              LEFT JOIN `srp_erp_segment`  on srp_erp_segment.segmentID=srp_employeesdetails.segmentID
              LEFT JOIN `srp_erp_employeemanagers` on EIdNo=empID AND active=1
              LEFT JOIN srp_employeesdetails manager on managerID=manager.EIdNo
              LEFT JOIN  (
                     SELECT EmpID AS empID_Dep, DepartmentDes FROM srp_departmentmaster AS departTB
                     JOIN srp_empdepartments AS empDep ON empDep.DepartmentMasterID = departTB.DepartmentMasterID
                     WHERE departTB.Erp_companyID=$com AND empDep.Erp_companyID=$com AND empDep.isActive=1 GROUP BY EmpID
              ) AS departTB ON departTB.empID_Dep=srp_employeesdetails.EIdNo
              WHERE srp_employeesdetails.Erp_companyID=$com  $filter";

        $result = $this->db->query($qry)->result_array();
        echo json_encode($result);
    }

    function confrim_leave_accrual()
    {
        $masterID = $this->input->post('masterID');
        $data['confirmedYN'] = 1;
        $data['confirmedby'] = current_userID();
        $data['confirmedDate'] = current_date();
        $update = $this->db->update('srp_erp_leaveaccrualmaster', $data, array('leaveaccrualMasterID' => $masterID));
        echo json_encode(array('s', 'Successfully confirmed'));
    }

    function deleteEmpAssignedShift()
    {
        $this->form_validation->set_rules('autoID', 'Auto ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Employee_model->deleteEmpAssignedShift());
        }
    }

    /*** Nasik ****/
    function setup_leaveApproval(){
        $companyID = current_companyID();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserGroup = current_user_group();
        $createdUserName = current_employee();
        $createdDateTime = current_date();

        $appLevel = $this->input->post('appLevel');
        $appType = $this->input->post('appType');


        $dataArr = [];
        foreach($appLevel as $key=>$row){
            $thisAppType = $appType[$key];
            $thisEmp = $this->input->post('empID_'.$row);


            if(empty($thisAppType)){
                die(
                    json_encode(['e', 'Please select a approval type for level '.$row])
                );
            }

            if($thisAppType == 3 && empty($thisEmp)){ /*** If approval type is HR manager than employee can not be blank ***/
                die(
                    json_encode(['e', 'Please select a employee for for level '.$row])
                );
            }


            $dataArr[$key]['approvalLevel'] = $row;
            $dataArr[$key]['approvalType'] = $thisAppType;
            $dataArr[$key]['companyID'] = $companyID;
            $dataArr[$key]['createdPCID'] = $createdPCID;
            $dataArr[$key]['createdUserGroup'] = $createdUserGroup;
            $dataArr[$key]['createdUserID'] = $createdUserID;
            $dataArr[$key]['createdUserName'] = $createdUserName;
            $dataArr[$key]['createdDateTime'] = $createdDateTime;
        }

        $this->db->trans_start();
        $this->db->where('companyID', $companyID)->delete('srp_erp_leaveapprovalsetup');
        $this->db->where('companyID', $companyID)->delete('srp_erp_leaveapprovalsetuphremployees');

        foreach($dataArr as $rowD){
            $this->db->insert('srp_erp_leaveapprovalsetup', $rowD);

            if($rowD['approvalType'] == 3){
                $id = $this->db->insert_id();
                $level = $rowD['approvalLevel'];
                $empArr = $this->input->post('empID_'.$level);
                $dataHr = [];
                foreach($empArr as $key=>$empID){
                    $dataHr[$key]['approvalSetupID'] = $id;
                    $dataHr[$key]['empID'] = $empID;
                    $dataHr[$key]['companyID'] = $companyID;
                    $dataHr[$key]['createdPCID'] = $createdPCID;
                    $dataHr[$key]['createdUserGroup'] = $createdUserGroup;
                    $dataHr[$key]['createdUserID'] = $createdUserID;
                    $dataHr[$key]['createdUserName'] = $createdUserName;
                    $dataHr[$key]['createdDateTime'] = $createdDateTime;
                }

                $this->db->insert_batch('srp_erp_leaveapprovalsetuphremployees', $dataHr);

            }

        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode( ['e', 'Error in leave approval setup'] );
        } else {
            $this->db->trans_commit();
            echo json_encode( ['s', 'leave approval setup successfully updated'] );
        }
    }

    function save_leave_approval_levels(){
        $this->form_validation->set_rules('level', 'Level', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $level = $this->input->post('level');

        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'LA');
        $this->db->update('srp_erp_documentcodemaster', ['approvalLevel'=>$level]);

        echo json_encode(['s', 'Level updated successfully']);
    }

    function saveGrade()
    {
        $this->form_validation->set_rules('gradeDescription', 'Grade', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 1, 'message' => validation_errors()));
        } else {
            echo json_encode($this->Employee_model->saveGrade());
        }
    }

    function fetch_grade()
    {
        $this->datatables->select('gradeID,gradeDescription', false)
            ->from('srp_erp_employeegrade');
        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        //$this->datatables->add_column('edit', '$1', 'editBoM(bomMasterID)');
        $this->datatables->add_column('edit', '<a onclick="editGrade($1,this)" data-description="$2"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp; | &nbsp;<a onclick="deleteGrade($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a> ', 'gradeID,gradeDescription');
        echo $this->datatables->generate();
    }


    function deleteGrade()
    {
        echo json_encode($this->Employee_model->deleteGrade());
    }

    function employeeHistory(){
        $empID = $this->input->post('id');
        $historyCode = $this->input->post('code');


        if($historyCode == 'LA'){
            $data['empID'] = $empID;
            $leaveGroup = $this->db->query("SELECT leaveGroupID FROM srp_employeesdetails WHERE EIdNo =  {$data['empID']}")->row_array();
            $data['leavGroupID'] = $leaveGroup['leaveGroupID'];
            if ($leaveGroup['leaveGroupID'] == '') {
                $data['leaveType'] = array();
            } else {
                $data['leaveType'] = $this->db->query("SELECT srp_erp_leavegroupdetails.leaveTypeID,policyMasterID,description
                                                   FROM srp_erp_leavegroupdetails
                                                   LEFT JOIN srp_erp_leavetype ON srp_erp_leavegroupdetails.leaveTypeID = srp_erp_leavetype.leaveTypeID
                                                   WHERE leaveGroupID = {$data['leavGroupID']}  ORDER BY srp_erp_leavetype.description ASC")->result_array();
            }
            $this->load->view('system/hrm/ajax/ajax_leave_balance.php', $data);
        }


    }

    function employee_leave_balance_report()
    {

        $this->form_validation->set_rules('asOfDate', 'As of Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('empID[]', 'Employee', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo '';
        } else {
            $isPDF = $this->uri->segment(3);
            $data['isPDF'] = $isPDF;
            $companyID = current_companyID();
            $leaveType = $this->input->post('leaveType');
            $groupType = $this->input->post('groupType');
            $empID = $this->input->post('empID');
            $policyType = $this->input->post('policyType');
            $data['policyType'] = $policyType;

            $date_format_policy = date_format_policy();
            $asOfDate = $this->input->post('asOfDate');
            $datefilter = '';
            $monthfilter = '';

            $current_date = current_format_date();
            $data['asOfDate'] = $current_date;
            if ($asOfDate != '') {
                $asOfDate = input_format_date($asOfDate, $date_format_policy);
                $date = explode('-', $asOfDate);
                $year = $date[0];
                $month = $date[1];
                $datefilter = " AND endDate <= '$asOfDate'";
                $monthfilter = " AND year <= '{$year}' AND month <= '{$month}'";

                $data['asOfDate'] = $this->input->post('asOfDate');
            }
            $current = $this->input->post('current');

            $data['groupType'] = $groupType;


            switch ($groupType) {
                case 1:
                    $filter = '';
                    if (!empty($empID)) {
                        $str = "'" . implode("','", $empID) . "'";

                        $filter .= " AND EidNo IN($str)";
                    }

                    if ($leaveType != '') {

                        $sql = "SELECT EidNo,ECode, Ename2, t1.leaveTypeID, t2.description, 
                                IFNULL((SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail 
                                  LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID 
                                  WHERE confirmedYN = 1 $monthfilter AND srp_erp_leaveaccrualdetail.leaveType = {$leaveType} AND srp_erp_leaveaccrualdetail.empID = EidNo), 0
                                ) AS  entitled, 
                                IFNULL((SELECT SUM(days) FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.leaveTypeID = {$leaveType} AND srp_erp_leavemaster.empID = EidNo 
                                  AND approvedYN = 1 $datefilter), 0
                                ) AS leaveTaken, 
                                IFNULL((SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail LEFT JOIN `srp_erp_leaveaccrualmaster` ON 
                                  srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID 
                                  WHERE confirmedYN = 1 $monthfilter AND srp_erp_leaveaccrualdetail.leaveType = {$leaveType} AND srp_erp_leaveaccrualdetail.empID = EidNo), 0
                                ) AS accrued, isPaidLeave, 
                                (IFNULL((SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail LEFT JOIN `srp_erp_leaveaccrualmaster` 
                                  ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID WHERE confirmedYN = 1 $monthfilter 
                                  AND srp_erp_leaveaccrualdetail.leaveType = {$leaveType} AND srp_erp_leaveaccrualdetail.empID = EidNo), 0)) - IFNULL((SELECT SUM(days) 
                                  FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.leaveTypeID = {$leaveType} AND srp_erp_leavemaster.empID = EidNo 
                                  AND approvedYN = 1 $datefilter), 0
                                ) AS days 
                                FROM `srp_employeesdetails` 
                                LEFT JOIN `srp_erp_leavegroupdetails` AS t1 ON t1.leaveGroupID = srp_employeesdetails.leaveGroupID
                                LEFT JOIN srp_erp_leavetype AS t2 ON t1.leaveTypeID = t2.leaveTypeID
                                WHERE Erp_companyID={$companyID} $filter GROUP BY  EIdNo";
                        $data['error'] = '';
                        $data['balancedata'] = $this->db->query($sql)->result_array();
                    }

                    if ($leaveType == '') {
                        $data['balancedata'] = false;
                        $data['error'] = 'Please select Leave Type';
                    }
                    if (empty($data['balancedata'])) {
                        $data['balancedata'] = false;
                    }


                    $data['leaveType'] = '';
                    if ($leaveType != '') {
                        $leaveDisc = $this->db->query("select description from srp_erp_leavetype where leaveTypeID=$leaveType")->row_array();
                        $data['leaveType'] = $leaveDisc['description'];
                    }
                    break;
                case 2:
                    if (!empty($empID)) {
                        $str = "'" . implode("','", $empID) . "'";
                        $filterLeaveType = '';
                        if ($leaveType != '') {
                            $filterLeaveType = "AND leaveTypeID={$leaveType} ";
                        }

                        $qry = "SELECT leaveTypeID, description FROM srp_erp_leavetype WHERE companyID={$companyID} $filterLeaveType ";
                        $leaveqry = $this->db->query($qry)->result_array();
                        //echo '<pre>'; print_r($leaveqry); echo '</pre>';        die();

                        if (!empty($leaveqry)) {
                            $select = 'EIdNo,ECode,Ename2';

                            $yearFirstDate = date('Y-01-01', strtotime($asOfDate));
                            $carryForwardLogic = "IF( isCarryForward=0 AND leavGroupDet.policyMasterID=1, accrualDate BETWEEN '{$yearFirstDate}' AND '{$asOfDate}', accrualDate <= '{$asOfDate}') ";
                            $carryForwardLogic2 = "AND IF( isCarryForward=0 AND leavGroupDet.policyMasterID=1, endDate BETWEEN '{$yearFirstDate}' AND '{$asOfDate}', endDate <= '{$asOfDate}') ";

                            foreach ($leaveqry as $type) {

                                $desc = trim($type['leaveTypeID']);
                                $balance = '`'.$desc.'balance`';
                                $entitle = '`'.$desc . 'entitle`';
                                $taken = '`'.$desc . 'taken`';
                                $typeID = $type['leaveTypeID'];
                                if ($policyType == 2) {

                                    $select .= ",round(
                                                (   IFNULL(
                                                    ( SELECT SUM(hoursEntitled) FROM srp_erp_leaveaccrualdetail AS detailTB
                                                      JOIN (
                                                         SELECT leaveaccrualMasterID, confirmedYN,
                                                         CONCAT(`year`,'-',LPAD(`month`,2,'00'),'-01') AS accrualDate
                                                         FROM srp_erp_leaveaccrualmaster WHERE confirmedYN = 1 AND companyID={$companyID}
                                                      ) AS accMaster ON detailTB.leaveaccrualMasterID = accMaster.leaveaccrualMasterID
                                                      JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = detailTB.leaveGroupID 
                                                      AND leavGroupDet.leaveTypeID = '{$typeID}'
                                                      WHERE {$carryForwardLogic} AND leavGroupDet.policyMasterID={$policyType} AND detailTB.leaveType='{$typeID}'
                                                      AND (detailTB.cancelledLeaveMasterID = 0 OR detailTB.cancelledLeaveMasterID IS NULL)
                                                      AND detailTB.empID = EidNo
                                                    ), 0 ) ) -
                                                    IFNULL(
                                                    (SELECT SUM(hours) FROM srp_erp_leavemaster 
                                                     JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = srp_erp_leavemaster.leaveGroupID 
                                                     AND leavGroupDet.leaveTypeID = '{$typeID}'
                                                     WHERE srp_erp_leavemaster.leaveTypeID='{$typeID}' AND leavGroupDet.policyMasterID={$policyType} AND 
                                                     srp_erp_leavemaster.empID = EidNo AND (cancelledYN = 0 OR cancelledYN IS NULL) AND
                                                     approvedYN = 1 {$carryForwardLogic2}), 0
                                                ) , 2) AS $balance ,
                                                round(
                                                (IFNULL(
                                                    (SELECT SUM(hoursEntitled) FROM srp_erp_leaveaccrualdetail AS detailTB
                                                     JOIN (
                                                        SELECT leaveaccrualMasterID, confirmedYN,
                                                        CONCAT(`year`,'-',LPAD(`month`,2,'00'),'-01') AS accrualDate
                                                        FROM srp_erp_leaveaccrualmaster WHERE confirmedYN = 1 AND companyID={$companyID}
                                                     ) AS accMaster ON detailTB.leaveaccrualMasterID = accMaster.leaveaccrualMasterID
                                                     JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = detailTB.leaveGroupID 
                                                     AND leavGroupDet.leaveTypeID = '{$typeID}'
                                                     WHERE {$carryForwardLogic} AND leavGroupDet.policyMasterID={$policyType} AND detailTB.leaveType='{$typeID}'
                                                     AND (detailTB.cancelledLeaveMasterID = 0 OR detailTB.cancelledLeaveMasterID IS NULL) AND
                                                     detailTB.empID = EidNo), 0)
                                                ) , 2) AS $entitle,
                                                round(
                                                IFNULL(
                                                  (SELECT SUM(hours) FROM srp_erp_leavemaster 
                                                   JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = srp_erp_leavemaster.leaveGroupID 
                                                   AND leavGroupDet.leaveTypeID = '{$typeID}'
                                                   WHERE srp_erp_leavemaster.leaveTypeID='{$typeID}' AND leavGroupDet.policyMasterID={$policyType}
                                                   AND empID = EidNo AND approvedYN = 1 AND (cancelledYN = 0 OR cancelledYN IS NULL) {$carryForwardLogic2}), 0
                                                ) , 2) AS $taken ";

                                }
                                else {

                                    $select .= ", round(
                                                ( IFNULL(
                                                  (SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail AS detailTB
                                                   JOIN (
                                                        SELECT leaveaccrualMasterID, confirmedYN,
                                                        CONCAT(`year`,'-',LPAD(`month`,2,'00'),'-01') AS accrualDate
                                                        FROM srp_erp_leaveaccrualmaster WHERE confirmedYN = 1 AND companyID={$companyID}
                                                   ) AS accMaster ON detailTB.leaveaccrualMasterID = accMaster.leaveaccrualMasterID
                                                   JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = detailTB.leaveGroupID 
                                                   AND leavGroupDet.leaveTypeID = '{$typeID}'
                                                   WHERE {$carryForwardLogic} AND detailTB.leaveType = '{$typeID}' AND leavGroupDet.policyMasterID IN (1,3)
                                                   AND (detailTB.cancelledLeaveMasterID = 0 OR detailTB.cancelledLeaveMasterID IS NULL) AND detailTB.empID = EidNo
                                                   ), 0
                                                  ) -
                                                  IFNULL(
                                                    (SELECT SUM(days) FROM srp_erp_leavemaster 
                                                     JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = srp_erp_leavemaster.leaveGroupID 
                                                     AND leavGroupDet.leaveTypeID = '{$typeID}'
                                                     WHERE srp_erp_leavemaster.leaveTypeID = '{$typeID}' AND
                                                     (cancelledYN = 0 OR cancelledYN IS NULL) AND leavGroupDet.policyMasterID IN (1,3) AND
                                                     srp_erp_leavemaster.empID = EidNo AND approvedYN = 1 {$carryForwardLogic2}
                                                    ), 0
                                                  )
                                                ) , 2) AS $balance ,
                                                round(
                                                (IFNULL(
                                                  (SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail AS detailTB
                                                   JOIN (
                                                        SELECT leaveaccrualMasterID, confirmedYN,
                                                        CONCAT(`year`,'-',LPAD(`month`,2,'00'),'-01') AS accrualDate
                                                        FROM srp_erp_leaveaccrualmaster WHERE confirmedYN = 1 AND companyID={$companyID}
                                                   ) AS accMaster ON detailTB.leaveaccrualMasterID = accMaster.leaveaccrualMasterID                                                   
                                                   JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = detailTB.leaveGroupID 
                                                   AND leavGroupDet.leaveTypeID = '{$typeID}'
                                                   WHERE {$carryForwardLogic} AND detailTB.leaveType = '{$typeID}' AND leavGroupDet.policyMasterID IN (1,3)
                                                   AND (detailTB.cancelledLeaveMasterID = 0 OR detailTB.cancelledLeaveMasterID IS NULL) AND detailTB.empID = EidNo), 0
                                                   )
                                                ) , 2) AS $entitle,
                                                round(
                                                IFNULL(
                                                   ( SELECT SUM(days) FROM srp_erp_leavemaster 
                                                     JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = srp_erp_leavemaster.leaveGroupID 
                                                     AND leavGroupDet.leaveTypeID = '{$typeID}'
                                                     WHERE srp_erp_leavemaster.leaveTypeID='{$typeID}' AND leavGroupDet.policyMasterID IN (1,3) AND
                                                     srp_erp_leavemaster.empID = EidNo AND approvedYN = 1 AND (cancelledYN = 0 OR cancelledYN IS NULL)
                                                     {$carryForwardLogic2} ), 0
                                                ) , 2) AS $taken ";
                                }

                            }

                            $qselect = rtrim($select, ',');

                            $data['details'] = $this->db->query("SELECT $qselect FROM srp_employeesdetails WHERE EIdNo IN($str)
                                                                 AND Erp_companyID='{$companyID}'")->result_array();

                            //echo '<pre>'.$this->db->last_query().'</pre>'; //die();
                            $data['leaveqry'] = $leaveqry;

                        } else {
                            $data['details'] = false;
                            $data['leaveqry'] = false;
                            $data['error'] = 'No records found';
                        }
                    } else {
                        $data['details'] = false;
                        $data['leaveqry'] = false;
                        $data['error'] = 'Please Select a Employee';

                    }
                break;
            }


            $html = $this->load->view('system/hrm/ajax/load-employee-leave-balance-report', $data, true);

            if($isPDF == 'pdf'){
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4');
            } else{
                echo $html;
            }
        }
    }

    function employee_leave_balance_report_pdf()
    {
        $leaveType = $this->input->post('leaveType');
        $groupType = $this->input->post('groupType');
        $empID = $this->input->post('empID');
        $policyType= $this->input->post('policyType');
        $data['policyType']=$policyType;

        $date_format_policy = date_format_policy();
        $asOfDate = $this->input->post('asOfDate');
        $datefilter = '';
        $monthfilter = '';

        $current_date = current_format_date();
        $data['asOfDate'] = $current_date;
        if ($asOfDate != '') {
            $asOfDate = input_format_date($asOfDate, $date_format_policy);
            $date = explode('-', $asOfDate);
            $year = $date[0];
            $month = $date[1];
            $datefilter = " AND endDate <= '$asOfDate'";
            $monthfilter = " AND year <= '{$year}' AND month <= '{$month}'";

            $data['asOfDate'] = $this->input->post('asOfDate');
        }
        $current = $this->input->post('current');
        $companyID = current_companyID();

        $data['groupType'] = $groupType;

        /*   if($leaveType !=''){*/

        switch ($groupType) {
            case 1:
                $filter = '';
                if (!empty($empID)) {
                    $str = "'" . implode("','", $empID) . "'";

                    $filter .= " AND EidNo IN($str)";
                }

                if ($leaveType != '') {

                    $sql = "SELECT EidNo,ECode, Ename2, t1.leaveTypeID, t2.description, IFNULL((SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID WHERE confirmedYN = 1 $monthfilter AND srp_erp_leaveaccrualdetail.leaveType = {$leaveType} AND srp_erp_leaveaccrualdetail.empID = EidNo), 0) AS  entitled, IFNULL((SELECT SUM(days) FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.leaveTypeID = {$leaveType} AND srp_erp_leavemaster.empID = EidNo AND approvedYN = 1 $datefilter), 0) AS leaveTaken, IFNULL((SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID WHERE confirmedYN = 1 $monthfilter AND srp_erp_leaveaccrualdetail.leaveType = {$leaveType} AND srp_erp_leaveaccrualdetail.empID = EidNo), 0) AS accrued, isPaidLeave, (IFNULL((SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID WHERE confirmedYN = 1 $monthfilter AND srp_erp_leaveaccrualdetail.leaveType = {$leaveType} AND srp_erp_leaveaccrualdetail.empID = EidNo), 0)) - IFNULL((SELECT SUM(days) FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.leaveTypeID = {$leaveType} AND srp_erp_leavemaster.empID = EidNo AND approvedYN = 1 $datefilter), 0) AS days FROM `srp_employeesdetails` LEFT JOIN `srp_erp_leavegroupdetails` AS t1 ON t1.leaveGroupID = srp_employeesdetails.leaveGroupID LEFT JOIN srp_erp_leavetype AS t2 ON t1.leaveTypeID = t2.leaveTypeID  WHERE Erp_companyID={$companyID} $filter GROUP BY  EIdNo";
                    $data['error'] = '';
                    $data['balancedata'] = $this->db->query($sql)->result_array();
                }

                if ($leaveType == '') {
                    $data['balancedata'] = false;
                    $data['error'] = 'Please select Leave Type';
                }
                if (empty($data['balancedata'])) {
                    $data['balancedata'] = false;
                }


                $data['leaveType'] = '';
                if ($leaveType != '') {
                    $leaveDisc = $this->db->query("select description from srp_erp_leavetype where leaveTypeID=$leaveType")->row_array();
                    $data['leaveType'] = $leaveDisc['description'];
                }
                break;
            case 2:

                if (!empty($empID)) {
                    $str = "'" . implode("','", $empID) . "'";
                    $filterLeaveType = '';
                    if ($leaveType != '') {
                        $filterLeaveType = "AND leaveTypeID={$leaveType} ";
                    }
                    $companyID = current_companyID();
                    $qry = "SELECT * FROM srp_erp_leavetype WHERE companyID={$companyID} $filterLeaveType  ";
                    $leaveqry = $this->db->query($qry)->result_array();

                    if (!empty($leaveqry)) {
                        $select = '';

                        foreach ($leaveqry as $type) {

                            $desc = str_replace(' ', '', $type['description']);
                            $balance = $desc . 'balance';
                            $entitle = $desc . 'entitle';
                            $taken = $desc . 'taken';

                            if($policyType==2){

                                $select .= "EIdNo,ECode,Ename2, (IFNULL((SELECT SUM(hoursEntitled) FROM srp_erp_leaveaccrualdetail LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID WHERE confirmedYN = 1 $monthfilter AND srp_erp_leaveaccrualdetail.leaveType = " . $type['leaveTypeID'] . " AND srp_erp_leaveaccrualdetail.empID = EidNo), 0)) - IFNULL((SELECT SUM(hours) FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.leaveTypeID = " . $type['leaveTypeID'] . " AND srp_erp_leavemaster.empID = EidNo AND approvedYN = 1 $datefilter), 0) AS $balance ,(IFNULL((SELECT SUM(hoursEntitled) FROM srp_erp_leaveaccrualdetail LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID WHERE confirmedYN = 1 $monthfilter AND srp_erp_leaveaccrualdetail.leaveType = " . $type['leaveTypeID'] . " AND srp_erp_leaveaccrualdetail.empID = EidNo), 0)) AS $entitle,IFNULL((SELECT SUM(hours) FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.leaveTypeID = " . $type['leaveTypeID'] . " AND srp_erp_leavemaster.empID = EidNo AND approvedYN = 1 $datefilter), 0) AS $taken,";

                            }else{

                                $select .= "EIdNo,ECode,Ename2, (IFNULL((SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID WHERE confirmedYN = 1 $monthfilter AND srp_erp_leaveaccrualdetail.leaveType = " . $type['leaveTypeID'] . " AND srp_erp_leaveaccrualdetail.empID = EidNo), 0)) - IFNULL((SELECT SUM(days) FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.leaveTypeID = " . $type['leaveTypeID'] . " AND srp_erp_leavemaster.empID = EidNo AND approvedYN = 1 $datefilter), 0) AS $balance ,(IFNULL((SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID WHERE confirmedYN = 1 $monthfilter AND srp_erp_leaveaccrualdetail.leaveType = " . $type['leaveTypeID'] . " AND srp_erp_leaveaccrualdetail.empID = EidNo), 0)) AS $entitle,IFNULL((SELECT SUM(days) FROM srp_erp_leavemaster WHERE srp_erp_leavemaster.leaveTypeID = " . $type['leaveTypeID'] . " AND srp_erp_leavemaster.empID = EidNo AND approvedYN = 1 $datefilter), 0) AS $taken,";

                            }

                        }
                        $select;
                        $qselect = rtrim($select, ',');

                        $data['details'] = $this->db->query("select $qselect from srp_employeesdetails WHERE EIdNo IN($str)")->result_array();
                        $data['leaveqry'] = $leaveqry;

                    }else{
                        $data['details'] = false;
                        $data['leaveqry'] = false;
                        $data['error'] = 'No records found';
                    }
                } else {
                    $data['details'] = false;
                    $data['leaveqry'] = false;
                    $data['error'] = 'Please Select a Employee';

                }

                break;
        }

        $html = $this->load->view('system/hrm/ajax/load-employee-leave-balance-report-pdf', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');


    }

    function save_access_right_master(){
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        }
        else {

            $companyID = current_companyID();
            $description = trim($this->input->post('description'));

            $isExist = $this->db->query("SELECT groupName FROM srp_erp_payrollgroups WHERE companyID={$companyID}
                                         AND groupName='{$description}'")->row('groupName');

            if (empty($isExist)) {
                $data = array(
                    'groupName' => $description,
                    'companyID' => $companyID,
                    'createdPCID' => current_pc(),
                    'createdUserGroup' => current_user_group(),
                    'createdUserID' => current_userID(),
                    'createdUserName' => current_employee(),
                    'createdDateTime' => current_date()
                );

                $this->db->insert('srp_erp_payrollgroups', $data);
                if ($this->db->affected_rows() > 0) {
                    $insertID = $this->db->insert_id();
                    echo json_encode(['s', 'Group master successfully created', $insertID, $description]);
                } else {
                    echo json_encode(['e', 'Error in group master create process']);
                }
            } else {
                echo json_encode(['e', 'This description is already exist.']);
            }
        }
    }

    function fetch_payroll_group_master()
    {
        $this->datatables->select("groupID,groupName");
        $this->datatables->from('srp_erp_payrollgroups');
        $this->datatables->where('companyID', current_companyID());
        $this->datatables->add_column('edit', '$1', 'payroll_group_master_action(groupID,groupName)');
        echo $this->datatables->generate();
    }

    function delete_groupSetup(){
        $masterID = $this->input->post('masterID');
        $companyID = current_companyID();

        $salaryDec = $this->db->query("SELECT documentSystemCode AS dCode FROM srp_erp_salarydeclarationmaster AS t1
                                     JOIN srp_erp_salarydeclarationdetails AS t2 ON salarydeclarationMasterID=declarationMasterID
                                     WHERE t1.companyID={$companyID} AND t2.companyID={$companyID} AND accessGroupID={$masterID}
                                     GROUP BY declarationMasterID")->result_array();

        $monthlyA = $this->db->query("SELECT monthlyAdditionsCode AS dCode FROM srp_erp_pay_monthlyadditionsmaster AS t1
                                     JOIN srp_erp_pay_monthlyadditiondetail AS t2 ON t1.monthlyAdditionsMasterID=t2.monthlyAdditionsMasterID
                                     WHERE t1.companyID={$companyID} AND t2.companyID={$companyID} AND accessGroupID={$masterID}
                                     GROUP BY t1.monthlyAdditionsMasterID")->result_array();

        $monthlyD = $this->db->query("SELECT monthlyDeductionCode AS dCode FROM srp_erp_pay_monthlydeductionmaster AS t1
                                     JOIN srp_erp_pay_monthlydeductiondetail AS t2 ON t1.monthlyDeductionMasterID=t2.monthlyDeductionMasterID
                                     WHERE t1.companyID={$companyID} AND t2.companyID={$companyID} AND accessGroupID={$masterID}
                                     GROUP BY t1.monthlyDeductionMasterID")->result_array();

        $payroll = $this->db->query("SELECT documentCode AS dCode FROM srp_erp_payrollmaster AS t1
                                     JOIN srp_erp_payrollheaderdetails AS t2 ON t1.payrollMasterID=t2.payrollMasterID
                                     WHERE t1.companyID={$companyID} AND t2.companyID={$companyID} AND accessGroupID={$masterID}
                                     GROUP BY t1.payrollMasterID")->result_array();

        $nPayroll = $this->db->query("SELECT documentCode AS dCode FROM srp_erp_non_payrollmaster AS t1
                                     JOIN srp_erp_non_payrollheaderdetails AS t2 ON t1.payrollMasterID=t2.payrollMasterID
                                     WHERE t1.companyID={$companyID} AND t2.companyID={$companyID} AND accessGroupID={$masterID}
                                     GROUP BY t1.payrollMasterID")->result_array();

        $count = count($salaryDec) + count($monthlyA) + count($monthlyD) + count($payroll) + count($nPayroll);

        if($count > 0 ){
            $str = '';
            if(!empty($salaryDec)){
                $str = '<span class="symbolSty"> Salary declarations</span><br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;- ';
                $str .= implode('<br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;- ', array_column($salaryDec, 'dCode'));
            }
            if(!empty($monthlyA)){
                $str .= ($str != '')? '<br/>':'';
                $str .= '<span class="symbolSty"> Monthly additions</span><br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;- ';
                $str .= implode('<br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;- ', array_column($monthlyA, 'dCode'));
            }
            if(!empty($monthlyD)){
                $str .= ($str != '')? '<br/>':'';
                $str .= '<span class="symbolSty"> Monthly deductions</span><br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;- ';
                $str .= implode('<br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;- ', array_column($monthlyD, 'dCode'));
            }
            if(!empty($payroll)){
                $str .= ($str != '')? '<br/>':'';
                $str .= '<span class="symbolSty"> Payrolls</span><br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; -';
                $str .= implode('<br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;- ', array_column($payroll, 'dCode'));
            }
            if(!empty($nPayroll)){
                $str .= ($str != '')? '<br/>':'';
                $str .= '<span class="symbolSty"> Non payrolls</span><br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;- ';
                $str .= implode('<br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;- ', array_column($nPayroll, 'dCode'));
            }

            $str = '<h4 style="font-weight: 600;">This group has been assigned to access following documents</h4><hr>'.$str;

            die( json_encode(['w', $str]) );
        }

        $this->db->trans_start();

        $where = ['companyID'=> $companyID, 'groupID'=>$masterID];

        $this->db->where($where)->delete('srp_erp_payrollgroups');
        $this->db->where($where)->delete('srp_erp_payrollgroupemployees');
        $this->db->where($where)->delete('srp_erp_payrollgroupincharge');

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            echo json_encode(['s', 'Group deleted successfully.']);
        }else{
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in process.']);
        }

    }

    function ajax_update_groupMaster()
    {
        $companyID = current_companyID();
        $masterID = $this->input->get('masterID');
        $description = trim($this->input->post('value'));

        if ($description == '') {
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Description is required.');
        }

        $where = [
            'companyID' => $companyID,
            'groupID' => $masterID,
        ];

        $isExist = $this->db->query("SELECT groupID FROM srp_erp_payrollgroups WHERE companyID={$companyID}
                                     AND groupName='{$description}'")->row('groupID');


        if (!empty($isExist) && $isExist != $masterID) {
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('This description is already exist.');
        }

        $data = [
            'groupName' => $description,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => current_date()
        ];

        $this->db->trans_start();
        $this->db->where($where)->update('srp_erp_payrollgroups', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo json_encode(['s', 'Description updated successfully']);
        } else {
            $this->db->trans_rollback();
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Error in description Update process');
        }
    }

    public function get_employees_for_access_rights()
    {
        $companyID = current_companyID();
        $groupID = $this->input->post('groupID');
        $empType = $this->input->post('empType');
        $segmentID = $this->input->post('segmentID');
        $designationFilter = $this->input->post('designationFilter');


        $this->datatables->select('EIdNo, ECode, Ename2 AS empName, desigTB.DesDescription AS designationStr, segTB.segmentCode AS segTBCode');
        $this->datatables->from('srp_employeesdetails AS empTB');
        $this->datatables->join('srp_designation', 'empTB.EmpDesignationId = srp_designation.DesignationID');
        $this->datatables->join('srp_erp_currencymaster AS cur', 'cur.currencyID = empTB.payCurrencyID');
        $this->datatables->join('srp_erp_segment AS segTB', 'segTB.segmentID = empTB.segmentID');
        $this->datatables->join('srp_designation AS desigTB', 'desigTB.DesignationID = empTB.EmpDesignationId');
        $this->datatables->add_column('addBtn', '$1', 'addBtn()');
        $this->datatables->where('empTB.Erp_companyID', $companyID);
        $this->datatables->where('empTB.isPayrollEmployee', 1);
        $this->datatables->where('isDischarged != 1');

        if($empType == 'employee'){
            $this->datatables->join("(SELECT employeeID FROM srp_erp_payrollgroupemployees WHERE companyID={$companyID}) AS grpEmp",
                'grpEmp.employeeID = EIdNo', 'left');
            $this->datatables->where('grpEmp.employeeID IS NULL');
        }

        if($empType == 'in-charge'){
            $this->datatables->where("empTB.EIdNo NOT IN(
                                        SELECT empID FROM srp_erp_payrollgroupincharge
                                        WHERE companyID={$companyID} AND groupID ={$groupID}
                                     )");
        }

        if (!empty($segmentID)) {
            $this->datatables->where('segTB.segmentID IN (' . $segmentID . ')');
        }

        if (!empty($designationFilter)) {
            $this->datatables->where('desigTB.DesignationID IN (' . $designationFilter . ')');
        }

        echo $this->datatables->generate();
    }

    function pull_employee_group(){
        $this->form_validation->set_rules('empList[]', 'Employee', 'trim|required');
        $this->form_validation->set_rules('masterID', 'Master ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        }
        else {

            $masterID = trim($this->input->post('masterID'));
            $empList = $this->input->post('empList');
            $companyID = current_companyID();

            $pcID = current_pc();
            $userID = current_userID();
            $userName = current_employee();
            $userGroup = current_user_group();
            $current_date = current_date();
            $data = [];

            foreach($empList as $key=>$row){
                $data[$key]['employeeID'] = $row;
                $data[$key]['groupID'] = $masterID;
                $data[$key]['companyID'] = $companyID;
                $data[$key]['createdPCID'] = $pcID;
                $data[$key]['createdUserID'] = $userID;
                $data[$key]['createdUserName'] = $userName;
                $data[$key]['createdUserGroup'] = $userGroup;
                $data[$key]['createdDateTime'] = $current_date;
            }

            $this->db->trans_start();
            $this->db->insert_batch('srp_erp_payrollgroupemployees', $data);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in process']);
            } else {
                $this->db->trans_commit();
                echo json_encode(['s', 'Employee successfully added']);
            }
        }
    }

    function pull_in_charge_group(){
        $this->form_validation->set_rules('empList[]', 'Employee', 'trim|required');
        $this->form_validation->set_rules('masterID', 'Master ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        }
        else {

            $masterID = trim($this->input->post('masterID'));
            $empList = $this->input->post('empList');
            $companyID = current_companyID();

            $pcID = current_pc();
            $userID = current_userID();
            $userName = current_employee();
            $userGroup = current_user_group();
            $current_date = current_date();
            $data = [];

            foreach($empList as $key=>$row){
                $data[$key]['empID'] = $row;
                $data[$key]['groupID'] = $masterID;
                $data[$key]['companyID'] = $companyID;
                $data[$key]['createdPCID'] = $pcID;
                $data[$key]['createdUserID'] = $userID;
                $data[$key]['createdUserName'] = $userName;
                $data[$key]['createdUserGroup'] = $userGroup;
                $data[$key]['createdDateTime'] = $current_date;
            }

            $this->db->trans_start();
            $this->db->insert_batch('srp_erp_payrollgroupincharge', $data);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in process']);
            } else {
                $this->db->trans_commit();
                echo json_encode(['s', 'In-charges successfully added']);
            }



            /*} else {
                echo json_encode(['e', 'This description is already exist.']);
            }*/
        }
    }

    function load_group_employee(){
        $groupID = $this->input->post('groupID');
        $str = '<span class="glyphicon glyphicon-trash" onclick="removeEmployee($1, \'employees\')" style="color:#d15b47;"></span>';

        $this->datatables->select("EIdNo, CONCAT(EmpSecondaryCode, ' - ', Ename2) AS empName, segTB.segmentCode AS segTBCode, DesDescription")
            ->from('srp_erp_payrollgroupemployees AS gEmp')
            ->join('srp_employeesdetails AS empTB', 'empTB.EIdNo=gEmp.employeeID')
            ->join('srp_erp_segment AS segTB', 'segTB.segmentID = empTB.segmentID')
            ->join('srp_designation AS desigTB', 'desigTB.DesignationID = empTB.EmpDesignationId')
            ->where('gEmp.companyID', current_companyID())
            ->where('empTB.Erp_companyID', current_companyID())
            ->where('groupID', $groupID)
            ->add_column('edit', '<div align="center">'.$str.'</div>', 'EIdNo');
        echo $this->datatables->generate();
    }

    function load_in_charge_employee(){
        $groupID = $this->input->post('groupID');
        $str = '<span class="glyphicon glyphicon-trash" onclick="removeEmployee($1, \'in-charge\')" style="color:#d15b47;"></span>';

        $this->datatables->select("EIdNo, CONCAT(EmpSecondaryCode, ' - ', Ename2) AS empName, DesDescription, '' AS CurrencyCode ")
            ->from('srp_erp_payrollgroupincharge AS gEmp')
            ->join('srp_employeesdetails AS empTB', 'empTB.EIdNo=gEmp.empID')
            ->join('srp_designation AS desigTB', 'desigTB.DesignationID = empTB.EmpDesignationId')
            ->where('gEmp.companyID', current_companyID())
            ->where('empTB.Erp_companyID', current_companyID())
            ->where('groupID', $groupID)
            ->add_column('edit', '<div align="center">'.$str.'</div>', 'EIdNo');
        echo $this->datatables->generate();
    }

    function assign_in_charges(){
        $this->form_validation->set_rules('empID[]', 'Employee', 'trim|required');
        $this->form_validation->set_rules('groups[]', 'Groups ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $groups = $this->input->post('groups');
        $empID = $this->input->post('empID');
        $companyID = current_companyID();
        $empList = implode(',', $empID);
        $pcID = current_pc();
        $userID = current_userID();
        $userName = current_employee();
        $userGroup = current_user_group();
        $current_date = current_date();

        $existingEmp = [];
        $insert_data = [];
        $i = 0;
        foreach($groups as $grp){
            $data = $this->db->query("SELECT groupMaster.groupID, groupName, inChargeTB.empID, CONCAT(ECode,' - ',Ename2) AS empName
                                FROM srp_erp_payrollgroups AS groupMaster
                                JOIN srp_erp_payrollgroupincharge AS inChargeTB ON groupMaster.groupID = inChargeTB.groupID
                                JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=inChargeTB.empID
                                WHERE groupMaster.companyID = '{$companyID}' AND groupMaster.companyID = '{$companyID}'
                                AND empTB.Erp_companyID = '{$companyID}' AND groupMaster.groupID={$grp}
                                AND empID IN ({$empList})")->result_array();

            if(!empty($data)){
                $existingEmp[$grp] = $data;
            }

            if(empty($existingEmp)){
                foreach($empID as $emp_row){
                    $insert_data[$i]['empID'] = $emp_row;
                    $insert_data[$i]['groupID'] = $grp;
                    $insert_data[$i]['companyID'] = $companyID;
                    $insert_data[$i]['createdPCID'] = $pcID;
                    $insert_data[$i]['createdUserID'] = $userID;
                    $insert_data[$i]['createdUserName'] = $userName;
                    $insert_data[$i]['createdUserGroup'] = $userGroup;
                    $insert_data[$i]['createdDateTime'] = $current_date;
                    $i++;
                }
            }
        }

        if(!empty($existingEmp)){

            $str = '';
            foreach($existingEmp as $key=>$ex_employee){
                $groupName = $ex_employee[0]['groupName'];
                $str .= ($str != '')? '<br/>': '';
                $str .= '<span class="symbolSty"> '.$groupName .'</span><br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;- ';
                $str .= implode('<br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;- ', array_column($ex_employee, 'empName'));

            }

            $str = '<h4 style="font-weight: 600;">Following employees already exist in following groups</h4><hr>'.$str;
            die( json_encode(['w', $str]) );
        }

        //echo '<pre>'; print_r($insert_data); echo '</pre>';        die();

        $this->db->trans_start();
        $this->db->insert_batch('srp_erp_payrollgroupincharge', $insert_data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error process']);
        } else {
            $this->db->trans_commit();
            echo json_encode(['s', 'In-charges successfully added']);
        }
    }

    function removeSingle_emp_payrollGroup(){
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('masterID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('removeType', 'Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        }
        else {
            $masterID = trim($this->input->post('masterID'));
            $empID = $this->input->post('empID');
            $removeType = $this->input->post('removeType');
            $companyID = current_companyID();

            $this->db->trans_start();

            $where = [
                'companyID' => $companyID,
                'groupID' => $masterID
            ];

            $empColumn = ($removeType == 'employees')? 'employeeID' : 'empID';
            $tableName = ($removeType == 'employees')? 'srp_erp_payrollgroupemployees' : 'srp_erp_payrollgroupincharge';
            $msg = ($removeType == 'employees')? 'Employee' : 'In-charge';
            $where[$empColumn] = $empID;

            $this->db->where($where)->delete($tableName);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in process']);
            } else {
                $this->db->trans_commit();
                echo json_encode(['s', $msg.' deleted successfully']);
            }
        }
    }

    function remove_all_emp_payrollGroup(){
        $this->form_validation->set_rules('masterID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('removeType', 'Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        }
        else {
            $masterID = trim($this->input->post('masterID'));
            $removeType = $this->input->post('removeType');
            $companyID = current_companyID();

            $this->db->trans_start();

            $where = [
                'companyID' => $companyID,
                'groupID' => $masterID
            ];

            $tableName = ($removeType == 'employees')? 'srp_erp_payrollgroupemployees' : 'srp_erp_payrollgroupincharge';
            $msg = ($removeType == 'employees')? 'Employee' : 'In-charge';

            $this->db->where($where)->delete($tableName);


            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in process']);
            } else {
                $this->db->trans_commit();
                echo json_encode(['s', $msg.' deleted successfully']);
            }
        }
    }

    function employee_list_by_segment(){
        $empArr = employee_list_by_segment();

        $html = '<select name="empID[]" id="empID" class="form-control" multiple="multiple"  required>';

        if ($empArr){
            foreach ($empArr as $empID) {
                $html .= '<option value="' . $empID['EIdNo'] . '">' . $empID['ECode'] . '|' . $empID['Ename2'] . '</option>';
            }
        }
        $html .= '</select>';

        echo json_encode(['s', $html]);

    }

    function employee_details_report(){
        $isForPrint = ($this->uri->segment(3) == 'Print')? 'Y' :'N';
        $this->form_validation->set_rules('empID[]', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('columns[]', 'Columns', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['e', validation_errors()]));
        }

        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $columns2 = $columns = $this->input->post('columns');
        $empID = $this->input->post('empID');


        if(in_array('EDOB', $columns2)){
            $key = array_search ('EDOB', $columns2);
            $columns2[$key] = 'DATE_FORMAT(EDOB,\'' . $convertFormat . '\') AS EDOB';
        }
        if(in_array('EDOJ', $columns2)){
            $key = array_search ('EDOJ', $columns2);
            $columns2[$key] = 'DATE_FORMAT(EDOJ,\'' . $convertFormat . '\') AS EDOJ';
        }
        if(in_array('DateAssumed', $columns2)){
            $key = array_search ('DateAssumed', $columns2);
            $columns2[$key] = 'DATE_FORMAT(DateAssumed,\'' . $convertFormat . '\') AS DateAssumed';
        }
        //echo '<pre>'; print_r($columns2); echo '</pre>'; die();

        $empIDList = implode(',', $empID);
        $select = implode(', ', $columns2);

        $detail = $this->db->query("SELECT $select
                                    FROM srp_employeesdetails AS empTB
                                    LEFT JOIN (
                                        SELECT empID, Ename2 AS reportManger FROM srp_erp_employeemanagers
                                        JOIN srp_employeesdetails ON managerID=EIdNo
                                        WHERE active=1 AND companyID={$companyID}
                                    ) AS mangerTB ON mangerTB.empID = EIdNo
                                    LEFT JOIN (
                                        SELECT segmentID, description AS segmentStr FROM srp_erp_segment WHERE companyID={$companyID}
                                    ) AS segmentTB ON segmentTB.segmentID=empTB.segmentID
                                    LEFT JOIN (
                                        SELECT DesignationID, DesDescription AS designationStr FROM srp_designation WHERE Erp_companyID={$companyID}
                                    ) AS designationTB ON designationTB.DesignationID=empTB.EmpDesignationId
                                    LEFT JOIN (
                                        SELECT NId, Nationality AS nationalityStr FROM srp_nationality WHERE Erp_companyID={$companyID}
                                    ) AS nationalityTB ON nationalityTB.NId=empTB.Nid
                                    LEFT JOIN (
                                        SELECT RId, Religion AS religionStr FROM srp_religion WHERE Erp_companyID={$companyID}
                                    ) AS religionTB ON religionTB.RId=empTB.Rid
                                    LEFT JOIN (
                                        SELECT EmpContractTypeID, Description AS empTypeStr FROM srp_empcontracttypes WHERE Erp_companyID={$companyID}
                                    ) AS contractType ON contractType.EmpContractTypeID=empTB.EmployeeConType
                                    LEFT JOIN (
                                        SELECT gradeID, gradeDescription AS gradeStr FROM srp_erp_employeegrade WHERE companyID={$companyID}
                                    ) AS employeeGradeTB ON employeeGradeTB.gradeID=empTB.gradeID
                                    LEFT JOIN (
                                        SELECT EmpID, DepartmentDes AS departmentStr FROM srp_departmentmaster AS t1
                                        JOIN srp_empdepartments AS t2 ON t1.DepartmentMasterID=t2.DepartmentMasterID
                                        WHERE t1.Erp_companyID={$companyID} AND t2.Erp_companyID={$companyID} GROUP BY EmpID
                                    ) AS departmentsTB ON departmentsTB.EmpID=empTB.EIdNo
                                    LEFT JOIN (
                                        SELECT maritialstatusID, description AS maritialStr FROM srp_erp_maritialstatus
                                    ) AS maritialStatusTB ON maritialStatusTB.maritialstatusID=empTB.MaritialStatus
                                    LEFT JOIN (
                                        SELECT genderID, `name` AS genderStr FROM srp_erp_gender
                                    ) AS genderTB ON genderTB.genderID=empTB.Gender
                                    WHERE Erp_companyID={$companyID} AND EIdNo IN ({$empIDList})")->result_array();

        $columnList = "'".implode("','", $columns)."'";
        $columnTitle = $this->db->query("SELECT columnTitle FROM srp_erp_employeedetailreport WHERE
                                         columnName IN ({$columnList})")->result_array();
        $data['columnTitle'] = $columnTitle;
        $data['detail'] = $detail;
        $data['columns'] = $columns;
        $data['isForPrint'] = $isForPrint;

        if($isForPrint == 'N'){
            echo json_encode(['s', $this->load->view('system/hrm/ajax/employee-details-report', $data, true)]);
        }
        else{
            $html = $this->load->view('system/hrm/ajax/employee-details-report', $data, true);
            //die($html);
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4', 1);
        }


    }


    function mail_to(){
        $param["empName"] = 'Nasik';
        $param["body"] = 'Leave application   is approved';
        //karangoda82@gmail.com
        $mailData = [
            'approvalEmpID' => '10',
            'documentCode' => 'Q1001',
            'toEmail' => 'mmubashir@gulfenergy-int.com',
            'subject' => 'Image test',
            'param' => $param,
        ];

        send_approvalEmail($mailData);
        echo 'ok';
    }

    function attendaceVerifyRecords()
    {
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');

        $sql=" SELECT count(presentTypeID) as totalCount FROM srp_erp_pay_empattendancereview WHERE attendanceDate BETWEEN '{$fromDate}' AND '{$toDate}' AND presentTypeID=4 ";
        $data=  $this->db->query($sql)->row_array();
        echo json_encode(['s', $data['totalCount']]);
    }

    public function deleteall_attendanceMaster()
    {
        $this->form_validation->set_rules('floorID[]', 'Floor', 'required');
        $this->form_validation->set_rules('fromDate', 'From Date', 'required');
        $this->form_validation->set_rules('toDate', 'To Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
                echo json_encode($this->Employee_model->deleteall_attendanceMaster());
        }
    }
}



