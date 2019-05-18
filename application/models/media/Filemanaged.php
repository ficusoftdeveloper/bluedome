<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class FileManaged extends CI_Model {

    public function getRows($id = "") {
        $rows = [];
        if(!empty($id)) {
            $this->db->order_by('fid', 'DESC');
            $query = $this->db->get_where('file_managed', array('fid' => $id));
            if ($query) {
                $rows  = $query->row_array();
            }
        } else {
            $this->db->order_by('fid', 'DESC');
            $query = $this->db->get_where('file_managed', ['is_processed' => 0]);
            $rows = $query->result_array();
        }

        return $rows;
    }

    public function getProcessedRows($id = "") {
        $rows = [];
        if(!empty($id)) {
            $this->db->order_by('fid', 'DESC');
            $query = $this->db->get_where('file_managed', array('fid' => $id, 'is_processed' => 1));
            if ($query) {
                $rows  = $query->row_array();
            }
        } else {
            $this->db->order_by('fid', 'DESC');
            $query = $this->db->get_where('file_managed', ['is_processed' => 1]);
            $rows = $query->result_array();
        }

        return $rows;
    }

    public function getEnabledRows($id = "") {
        $rows = [];
        if(!empty($id)) {
            $this->db->order_by('fid', 'DESC');
            $query = $this->db->get_where('file_managed', array('fid' => $id, 'status' => 1));
            if ($query) {
                $rows  = $query->row_array();
            }
        } else {
            $this->db->order_by('fid', 'DESC');
            $query = $this->db->get_where('file_managed', ['status' => 1]);
            $rows = $query->result_array();
        }

        return $rows;
    }

    public function getPropsLatestRevision($fid) {
        $this->db->order_by('fpid', 'DESC');
        $query = $this->db->get_where('file_props', array('fid' => $fid));
        return $query->row_array();
    }

    public function getUploadedFiles() {
        $this->db->order_by('fid', 'DESC');
        $query = $this->db->get_where('file_managed', array('is_uploaded' => 1));
        return $query->result_array();
    }

    public function getRowsByImage($id = "") {
        if(!empty($id))
        {
            $this->db->order_by('fid', 'DESC');
            $query = $this->db->get_where('file_managed', array('fid' => $id, 'filetype' => 'image'));
            return $query->row_array();
        }
        else
        {
            $this->db->order_by('fid', 'DESC');
            $query = $this->db->get_where('file_managed', array('filetype' => 'image', 'is_uploaded' => 0));
                return $query->result_array();
            }
    }

    public function getRowsByVideo($id = ""){
        if(!empty($id))
        {
            $this->db->order_by('fid', 'DESC');
            $query = $this->db->get_where('file_managed', array('fid' => $id, 'filetype' => 'video'));
            return $query->row_array();
        }
        else
        {
            $this->db->order_by('fid', 'DESC');
            $query = $this->db->get_where('file_managed', array('filetype' => 'video', 'is_uploaded' => 0));
                return $query->result_array();
            }
    }

    public function add($data = array()) {
        $insert = $this->db->insert('file_managed', $data);
        if($insert){
            return $this->db->insert_id();
        }else{
            return false;
        }
    }

    public function insertFileProps($data = []) {
        $insert = $this->db->insert('file_props', $data);
        if($insert){
            return $this->db->insert_id();
        }else{
            return false;
        }
    }

    public function updateFileProps($fid, $data = []) {
      if (!empty($data) && $fid) {
        $update = $this->db->update('file_props', $data, ['fid' => $fid]);
        return $update ? TRUE : FALSE;
      } else {
        return FALSE;
      }
    }

    public function update($data, $id) {
        if(!empty($data) && !empty($id)){
            $update = $this->db->update('file_managed', $data, array('fid'=>$id));
            return $update?true:false;
        }else{
            return false;
        }
    }

    public function flushUnuploadedFiles() {
        $this->db->delete('file_managed', array('is_uploaded' => 0));
    }

    public function deleteFile($fid) {
        if (is_numeric($fid)) {
            $this->db->delete('file_managed', array('fid' => $fid));
            $this->db->delete('file_props', array('fid' => $fid));
            return true;
        }
        return false;
    }

    public function getMapMarkers($fid) {
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get_where('file_map_point', array('fid' => $fid));
        return $query->result_array();
    }
}