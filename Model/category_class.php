<?php 

class Category extends Dbh
{
    public function setCategory($CategoryNo, $CategoryName, $shop_SHID)
    {
        try 
        {
            $sql="INSERT INTO categories(CategoryNo, CategoryName, shop_SHID) VALUES(?,?,?);";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$CategoryNo, $CategoryName, $shop_SHID]);
        }//try 
        catch (PDOException $e) 
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//save category

    public function editCategory($CategoryName, $category_id)
    {
        try 
        {
            $sql="UPDATE categories SET CategoryName = ? WHERE CTID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$CategoryName, $category_id]);
        }//try 
        catch (PDOException $e) 
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//update category

    public function deleteCategory($category_id)
    {
        try 
        {
            $sql="DELETE FROM categories WHERE CTID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$category_id]);
        }//try 
        catch (PDOException $e) 
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//update category

    public function getCategoryCount($shop_id)
    {
        try{
            $sql = "SELECT max(CTID) AS CategoryCount FROM categories WHERE shop_SHID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$shop_id]);
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }
    }//get category count

    public function getCategoryByName($shop_id, $category_name)
    {
        try
        {
            $sql = "SELECT count(CTID) AS CategoryCount FROM categories WHERE CategoryName = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([ $category_name]);
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }
    }//get category by name

    public function getCategoryByShop($shop_id,$multi=null,$company=null)
    {
        try{
            if($multi==null)
            {
                $sql = "SELECT * FROM categories WHERE shop_SHID = ?;";
                $stmt = $this->connect()->prepare($sql);
                $stmt->execute([$shop_id]);
            }
            else
            {
                if($multi==1)
                {
                    $sql = "SELECT * FROM categories c 
                    INNER JOIN shop s ON s.SHID = c.shop_SHID
                    WHERE s.Company_CMID = ?;";
                    $stmt = $this->connect()->prepare($sql);
                    $stmt->execute([$company]);
                }
                else
                {
                    $sql = "SELECT * FROM categories WHERE shop_SHID = ?;";
                    $stmt = $this->connect()->prepare($sql);
                    $stmt->execute([$shop_id]);
                }
                
            }
            
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }   
    }//get category by shop

    public function getOneCategory($category_id)
    {
        try{
            $sql = "SELECT * FROM categories WHERE CTID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$category_id]);
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }   
    }//get category by shop

//=================================== Sub Categories =================================//
    public function setSubCategory($SubCatNo, $SubCatName, $categories_CTID)
    {
        try 
        {
            $sql="INSERT INTO subcategories(SubCatNo, SubCatName, categories_CTID) VALUES(?,?,?);";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$SubCatNo, $SubCatName, $categories_CTID]);
        }//try 
        catch (PDOException $e) 
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//save category

    public function editSubcategory($subcat_name, $category_id, $subcat_id)
    {
        try 
        {
            $sql="UPDATE subcategories SET SubCatName=? , categories_CTID=? WHERE SCID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$subcat_name, $category_id, $subcat_id]);
        }//try 
        catch (PDOException $e) 
        {
            die("Error: Unable to update data: " . $e->getMessage());
        }//catch
    }//update category

    public function deleteSubcategory($subcat_id)
    {
        try 
        {
            $sql="DELETE FROM subcategories WHERE SCID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$subcat_id]);
        }//try 
        catch (PDOException $e) 
        {
            die("Error: Unable to update data: " . $e->getMessage());
        }//catch
    }//DELETE category

    public function getOneSubCategory($subcat_id)
    {
        try{
            $sql = "SELECT * FROM subcategories
            INNER JOIN categories ON categories.CTID = subcategories.categories_CTID
            WHERE SCID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$subcat_id]);
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }   
    }//get category by shop

    public function getSubcategoryByName($subcat_name, $shop_id)
    {
        try
        {
            $sql = "SELECT * FROM subcategories
            INNER JOIN categories ON categories.CTID = subcategories.categories_CTID
            WHERE SubCatName = ? AND shop_SHID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$subcat_name, $shop_id]);
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }   
    }//get sub category by name

    public function getSubcategoryByShop($shop_id, $multi=null,$company=null)
    {
        try
        {
            
            if($multi==null)
            {
                $sql = "SELECT * FROM subcategories
                INNER JOIN categories ON categories.CTID = subcategories.categories_CTID
                WHERE shop_SHID = ?;";
                $stmt = $this->connect()->prepare($sql);
                $stmt->execute([$shop_id]);
            }
            else
            {
                if($multi==1)
                {
                    $sql = "SELECT * FROM subcategories sc 
                    INNER JOIN categories c ON c.CTID = sc.categories_CTID
                    INNER JOIN shop s ON s.SHID = c.shop_SHID
                    WHERE s.Company_CMID = ?;";
                    $stmt = $this->connect()->prepare($sql);
                    $stmt->execute([$company]);
                }
                else
                {
                    $sql = "SELECT * FROM subcategories
                    INNER JOIN categories ON categories.CTID = subcategories.categories_CTID
                    WHERE shop_SHID = ?;";
                    $stmt = $this->connect()->prepare($sql);
                    $stmt->execute([$shop_id]);
                }                
            }
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }   
    }//get sub category by name

    public function getSubcategoryByCatID($category_id)
    {
        try
        {
            $sql = "SELECT * FROM subcategories
            INNER JOIN categories ON categories.CTID = subcategories.categories_CTID
            WHERE categories_CTID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$category_id]);
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }   
    }//get sub category by name

    public function getSubcategoryCount($shop_id)
    {
        try
        {
            $sql = "SELECT max(SCID) as SubcatCount FROM subcategories";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }   
    }//get sub category by name
    public function getSubcatDuplicate($category_id, $subcat_name, $shop_id)
    {
        $sql = "SELECT * FROM subcategories
        INNER JOIN categories ON categories.CTID = subcategories.categories_CTID
        WHERE categories_CTID = ? AND SubCatName= ?;";
        $stmt = $this->connect()->prepare($sql);
        try
        {
            $stmt->execute([$category_id, $subcat_name]);
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }   
    }//get sub category by name
    public function getSubcatDuplicateid($category_id, $subcat_name, $SCID)
    {
        $sql = "SELECT COUNT(*) AS subcatCount FROM subcategories WHERE SubCatName = ? AND categories_CTID = ? AND SCID!=?;";
        $stmt = $this->connect()->prepare($sql);
        try
        {
            $stmt->execute([ $subcat_name, $category_id, $SCID]);
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }   
    }//get sub category by name
}//class category