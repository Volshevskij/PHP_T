<?php
 
class PersonsDataBase
{
   private int  $id;
   private string $firstName;
   private string $lastName;
   private DateTime $birthDate;
   private int $gender;
   private string $birthCity;

   public function __construct
   (
       $id = null, 
       $firstName = null, 
       $lastName = null,
       $birthDate = null,
       $gender = null,
       $birthCity = null,
       $connection,
       $dataBaseName) {
       
        if ($id != null) {
           $this->setId($id)
                ->getPersonFromDataBase($connection, $dataBaseName);
        } else {
           $this->setFirstName($firstName)
                ->setLastName($lastName)
                ->setBirthDate($birthDate)
                ->setGender($gender)
                ->setBirthCity($birthCity)
                ->savePersonToDataBase($connection, $dataBaseName);
        }
        
    }

    public function setId($id) {
        if (!is_int($id)) {
            throw new InvalidArgumentException('id must be an Integer value');
        }
        $this->id = $id;
        return $this;
    }

    public function setFirstName($firstName) {
        if (!is_string($firstName)) {
            throw new InvalidArgumentException('firstName must be a String value');
        }
        $this->firstName = $firstName;
        return $this;
    }

    public function setLastName($lastName) {
        if (!is_string($lastName)) {
            throw new InvalidArgumentException('lastName must be a String value');
        }
        $this->lastName = $lastName;
        return $this;
    }

    public function setBirthDate($birthDate) {
        if (!is_a($birthDate, 'DateTime')) {
            throw new InvalidArgumentException('birthDate must be a DateTime value');
        }
        $this->birthDate = $birthDate;
        return $this;
    }

    public function setGender($gender) {
        if ($gender != 0 && $gender != 1) {
            throw new InvalidArgumentException('Gender value must be 0 or 1 of Integer');
        }
        $this->gender = $gender;
        return $this;
    }

    public function setBirthCity($birthCity) {
        if (!is_string($birthCity)) {
            throw new InvalidArgumentException('birthCity must be a String value');
        }
        $this->birthCity = $birthCity;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getBirthDate() {
        return $this->birthDate;
    }

    public function getGender() {
        return $this->gender;
    }

    public function getBirthCity() {
        return $this->birthCity;
    }

    public function getPersonFromDataBase($connection,$dataBaseName) {
        $queryString = "SELECT firstname, lastname, birthDate, gender, birthCity FROM {$dataBaseName} WHERE id={$this->id}";
        
        if ($result = $connection -> query($queryString)) {
            // while ($obj = $result -> fetch_object()) {
            //     printf("%s (%s)\n", $obj->Lastname, $obj->Age);
            // }
            // $result -> free_result();
            
            $object = $result->fetch(PDO::FETCH_OBJ);

            if ($object != false) {
                $this->setFirstName($object->firstName)
                     ->setLastName($object->lastName)
                     ->setBirthDate($object->birthDate)
                     ->setGender($object->gender)
                     ->setBirthCity($object->birthCity);
            }

        }
    }

    public function savePersonToDataBase($connection, $dataBaseName) {
        $queryString = "INSERT INTO {$dataBaseName} (firstname, lastname, birthDate, gender, birthCity)
        VALUES ({$this->firstName}, {$this->lastName}, {$this->birthDate}, {$this->gender}, {$this->birthCity})";

        if ($connection->query($queryString) === true) {
            echo 'New record created successfully';
        } else {
            throw new Error('Error creating record: ' . $connection->error);
        }
    }

    public function deletePerson($connection, $dataBaseName) {
        // $servername = "localhost";
        // $username = "username";
        // $password = "password";
        // $dbname = "myDB";

        // // Create connection
        // $conn = new mysqli($servername, $username, $password, $dbname);
        // // Check connection
        // if ($conn->connect_error) {
        //     die("Connection failed: " . $conn->connect_error);
        // }

        $queryString = "DELETE FROM {$dataBaseName} WHERE id={$this->id}";

        if ($connection->query($queryString) === true) {
            echo "Person deleted successfully";
        } else {
            throw new Error('Error deleting record: ' . $connection->error);
        }

        //$conn->close();
    }

    public static function ConvertBirthDateToAge($birthDate) {
        if (!is_a($birthDate, 'DateTime')) {
            throw new InvalidArgumentException('birthDate must be DateTime format');
        }
        return $birthDate->diff(date('Y-m-d'))->y;
    }

    public static function ConvertGenderDigitToString($number) {
        if ($number != 0 && $number != 1) {
            throw new InvalidArgumentException('Gender value must be 0 or 1 of Integer');
        }

        if ($number == 0) {
            return 'муж';
        } else {
            return 'жен';
        }
    }

    public function formatPerson($formatGender = null, $formatBirthDate = null) {
        $newPerson = new stdClass();
        $newPerson->id = $this->getId();
        $newPerson->firstName = $this->getFirstName();
        $newPerson->lastName = $this->getLastName();
        $newPerson->birthCity = $this->getBirthCity();

        if ($formatGender != null && $formatGender != false) {
            $newPerson->gender = PersonsDataBase::ConvertGenderDigitToString($this->getGender());
        } else {
            $newPerson->gender = $this->getGender();
        }

        if ($formatBirthDate != null && $formatBirthDate != false) {
            $newPerson->birthDate = PersonsDataBase::ConvertBirthDateToAge($this->getBirthDate());
        } else {
            $newPerson->birthDate = $this->getBirthDate();
        }
        return $newPerson;
    }

    
}


