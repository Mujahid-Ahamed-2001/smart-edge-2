<?php 
class quote_class extends Dbh{
    public function getQuote_status($id=null){
        if($id!=null)
        {
            $sql = "SELECT * FROM quote_stat WHERE QSID = ?";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$id]);
        }
        else
        {
            $sql = "SELECT * FROM quote_stat";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }
    public function getusers($id=null){
        if($id!=null)
        {
            $sql = "SELECT * FROM user WHERE USID = ?";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$id]);
        }
        else
        {
            $sql = "SELECT * FROM user";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }
}
