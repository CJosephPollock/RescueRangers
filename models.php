<?php
    require('dbconnect.php');
    $dbq = db_connect();

        // getIncident(7852);



        // getIncidents();
        // $arrayToSend = [];
        // $pre_result = $dbq->prepare("SELECT * FROM PERSON");
        // $pre_result->execute();
        // while ($row = $pre_result->fetch()) {
        //     array_push($arrayToSend, $row);
        // }
        // header('Content-Type: application/json');
        // echo json_encode($arrayToSend, TRUE);
        //close connection


    try {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST))
            $_POST = json_decode(file_get_contents('php://input'), true);
        // $body = json_decode($_POST);
        // $body = print_r($_POST, true);
        // echo "<pre>";
        // echo $body;
        // echo "</pre>";

        // exit;
        $toCall = $_POST['target'];
        switch ($toCall) {
            case 'startIncident':
                startIncident();
                break;

            case 'getIncident':
                getIncident(7852);
                break;
            default:
                # code...
                break;
        }
        $dbq = NULL;
    } catch (PDOException $e) {
         print ("getMessage(): " . $e->getMessage () . "\n");
    }

    function sendResults($results) {
        $arrayToSend = [];
        foreach ($results as $row) {
            array_push($arrayToSend, $row);
        }
        header('Content-Type: application/json');
        echo json_encode($arrayToSend, TRUE);
    }


    function addPerson ($PersonFirstName, $PersonLastName, $PersonTypeName) {
        global $dbq;
        $sth = $dbh->prepare("exec popPerson @FirstName=:FirstName @LastName=:LastName @PersonTypeName=:PersonTypeName");
        $sth->bindParam(':FirstName', $PersonFirstName);
        $sth->bindParam(':LastName', $PersonLastName);
        $sth->bindParam(':PersonTypeName', $PersonTypeName);
        $sth->execute();
    }

    function getPersons () {
        global $dbq;
        $result = $dbq->prepare("SELECT * FROM PERSON");
        $result->execute();
        $results = $result->fetchAll();
        header('Content-Type: application/json');
        echo json_encode($results, TRUE);
    }

    function startIncident() {
        global $dbq;


        $data = [
            'IncidentName' => $_POST['IncidentName'],
            'IncidentDesc' => $_POST['IncidentDesc'],
            'IncidentTypeName' => $_POST['IncidentType'],
            'KitName' => $_POST['KitName'],
            'FirstName' => $_POST['PersonFirstName'],
            'LastName' => $_POST['PersonLastName'],
            'SeverityName' => $_POST['SeverityName'],
            'InjuryName' => $_POST['InjuryName'],
            'InjuryDesc' => $_POST['InjuryDesc'],
            'InjuryTypeName' => $_POST['InjuryType'],
            'LocationName' => $_POST['LocationName'],
            'LocationLat' =>  isset($_POST['Latitude']) ? $_POST['Latitude'] : NULL,
            'LocationLng' =>  isset($_POST['Longitude']) ? $_POST['Longitude'] : NULL,
            'StreetName' => isset($_POST['StreetName']) ? $_POST['StreetName'] : NULL,
            'City' =>  isset($_POST['City']) ? $_POST['City'] : NULL,
            'State' => isset($_POST['State']) ? $_POST['State'] : NULL,
            'Country' => isset($_POST['Country']) ? $_POST['Country'] : NULL
        ];

        try {
            $IncidentTypeID = $dbq->query("SELECT IncidentTypeID FROM INCIDENT_TYPE i WHERE i.IncidentTypeName = '$data[IncidentTypeName]'");
            $IncidentTypeID = intval($IncidentTypeID->fetchColumn());

            $PersonTypeID = $dbq->query("SELECT PersonTypeID FROM PERSON_TYPE pt WHERE pt.PersonTypeName = 'Client'");
            $PersonTypeID = intval($PersonTypeID->fetchColumn());

            $KitID = $dbq->query("SELECT KitID FROM KIT k WHERE k.KitName = '$data[KitName]'");
            $KitID = intval($KitID->fetchColumn());

            $SeverityID = $dbq->query("SELECT SeverityID FROM SEVERITY WHERE SeverityName = '$data[SeverityName]'");
            $SeverityID = intval($SeverityID->fetchColumn());

            $InjuryTypeID = $dbq->query("SELECT InjuryTypeID FROM INJURY_TYPE it WHERE it.InjuryTypeName = '$data[InjuryTypeName]'");
            $InjuryTypeID = intval($InjuryTypeID->fetchColumn());


            $LocationID = $dbq->query("INSERT INTO LOCATION (LocationName, LocationLat, LocationLng)
            VALUES ('$data[LocationName]', $data[LocationLat], $data[LocationLng])");
            $LocationID = $dbq->query("SELECT IDENT_CURRENT('LOCATION')");
            $LocationID = intval($LocationID->fetchColumn());

            $IncidentID = $dbq->query("INSERT INTO INCIDENT(IncidentName, IncidentDesc, IncidentTypeID, KitID, LocationID)
            VALUES('$data[IncidentName]', '$data[IncidentDesc]', $IncidentTypeID, $KitID, $LocationID)");
            $IncidentID = $dbq->query("SELECT IDENT_CURRENT('INCIDENT')");
            $IncidentID = intval($IncidentID->fetchColumn());

            $PersonID = $dbq->query("INSERT INTO PERSON (PersonFName, PersonLName, PersonTypeID)
            VALUES ('$data[FirstName]', '$data[LastName]', $PersonTypeID)");
            $PersonID = $dbq->query("SELECT IDENT_CURRENT('PERSON')");
            $PersonID = intval($PersonID->fetchColumn());


            $dbq->query("INSERT INTO PERSON_INCIDENT (PersonID, IncidentID)
            VALUES ($PersonID, $IncidentID)");

            $dbq->query("INSERT INTO INJURY (InjuryName, InjuryDesc, InjuryTypeID)
            VALUES ('$data[InjuryName]', '$data[InjuryDesc]', $InjuryTypeID)");
            $InjuryID = $dbq->query("SELECT IDENT_CURRENT('INJURY')");
            $InjuryID = intval($InjuryID->fetchColumn());


            $dbq->query("INSERT INTO PERSON_INJURY (PersonID, InjuryID, SeverityID)
            VALUES ($PersonID, $InjuryID, $SeverityID)");

            if ($data['StreetName'] !== NULL && $data['City'] !== NULL && $data['State'] !== NULL && $data['Country'] !== NULL) {
                $CountryID = $dbq->query("SELECT CountryID FROM COUNTRY WHERE COUNTRY.CountryName = '$data[Country]'");
                $CountryID = intval($CountryID->fetchColumn());

                $AddressID = $dbq->query("INSERT INTO ADDRESS (StreetName, City, [State], CountryID)
                                                                VALUES ('$data[StreetName]', '$data[City]', '$data[State]', $CountryID)");
                $AddressID = $dbq->query("SELECT IDENT_CURRENT('ADDRESS')");
                $AddressID = intval($AddressID->fetchColumn());


                $dbq->query("INSERT INTO PERSON_ADDRESS (PersonID, AddressID)
                    VALUES ($PersonID, $AddressID)");
            }



        } catch (PDOException $e) {
             print ("getMessage(): " . $e->getMessage () . "\n");
        }
    }

    function addIncident ($IncidentName, $IncidentDesc, $KitName, $LocationLat, $IncidentTypeName) {
        global $dbq;
        // $sth = $dbq->prepare("{ RRTest2.dbo.popIncident = CALL
        //                                     ?
        //                                     ?
        //                                     ?
        //                                     ?
        //                                     ?
        //                                     ? }");
        // $sth->bindParam(1, $IncidentDesc, PDO::PARAM_STR);
        // $sth->bindParam(2, $IncidentName, PDO::PARAM_STR);
        // $sth->bindParam(3, date("Y-m-d H:m:s"), PDO::PARAM_STR);
        // $sth->bindParam(4, $KitName, PDO::PARAM_STR);
        // $sth->bindParam(5, $LocationLat, PDO::PARAM_STR);
        // $sth->bindParam(6, $IncidentTypeName, PDO::PARAM_STR);
        // $sth->execute();
        $sql = "INSERT INTO INCIDENT (IncidentName, IncidentDesc, IncidentTypeID, KitID, LocationID) VALUES ('Test Incident', 'Test Desc', 1, 1, 1518)";
        $sth = $dbq->query($sql);
    }

    function getIncidents () {
        global $dbq;
        $incidents = $dbq->prepare("SELECT * FROM INCIDENT i
                                                        INNER JOIN INCIDENT_TYPE  it ON it.IncidentTypeID = i.IncidentTypeID
                                                        INNER JOIN KIT k ON k.KitID = i.KitID
                                                        INNER JOIN EQUIPMENT_KIT ek ON ek.KitID = k.KitID
                                                        INNER JOIN EQUIPMENT e ON e.EquipmentID = ek.EquipmentID
                                                        INNER JOIN LOCATION l ON l.LocationID = i.LocationID
                                                    ");
        $incidents->execute();
        $incidents = $incidents->fetchAll();
        $incidents = attachToIncidents($incidents);

        header('Content-Type: application/json');
        echo json_encode($incidents, TRUE);

    }

    function getIncident ($IncidentID) {
        global $dbq;

        $incidents = $dbq->prepare("SELECT * FROM INCIDENT i
                                                        INNER JOIN INCIDENT_TYPE  it ON it.IncidentTypeID = i.IncidentTypeID
                                                        INNER JOIN KIT k ON k.KitID = i.KitID
                                                        INNER JOIN EQUIPMENT_KIT ek ON ek.KitID = k.KitID
                                                        INNER JOIN EQUIPMENT e ON e.EquipmentID = ek.EquipmentID
                                                        INNER JOIN LOCATION l ON l.LocationID = i.LocationID

                                                        WHERE i.IncidentID = :IncidentID
                                                    ");
        $incidents->bindParam(':IncidentID', $IncidentID);

        $incidents->execute();
        $incidents = $incidents->fetchAll();
        $incidents = attachToIncidents($incidents);

        header('Content-Type: application/json');
        echo json_encode($incidents, TRUE);

    }

    function attachToIncidents($incidents) {
        global $dbq;
        // attach people and vehicles to incidents
        foreach ($incidents as &$incident) {
            $persons = $dbq->prepare("SELECT p.PersonID, p.PersonFName, p.PersonLName, pt.PersonTypeName FROM PERSON p
                                                        INNER JOIN PERSON_INCIDENT pi ON p.PersonID = pi.PersonID
                                                        INNER JOIN INCIDENT i ON i.IncidentID = pi.IncidentID
                                                        INNER JOIN PERSON_TYPE pt ON p.PersonTypeID = pt.PersonTypeID

                                                        WHERE i.IncidentID = :IncidentID
                                                    ");
            $persons->bindParam(':IncidentID', $incident['IncidentID']);
            $persons->execute();
            $persons = $persons->fetchAll();
            $persons = attachToPersons($persons);



            $vehicles = $dbq->prepare("SELECT * FROM INCIDENT i
                                                        INNER JOIN INCIDENT_VEHICLE iv ON iv.IncidentID = i.IncidentID
                                                        INNER JOIN VEHICLE v ON v.VehicleID = iv.VehicleID
                                                        INNER JOIN VEHICLE_TYPE vt ON vt.VehicleTypeID = v.VehicleTypeID

                                                        WHERE i.IncidentID = :IncidentID
                                                    ");
            $vehicles->bindParam(':IncidentID', $incident['IncidentID']);
            $vehicles->execute();
            $vehicles = $vehicles->fetchAll();
            $vehicles = attachToVehicles($vehicles);

            $equipment = $dbq->prepare("SELECT * FROM KIT k
                                                        INNER JOIN EQUIPMENT_KIT ek ON ek.KitID = k.KitID
                                                        INNER JOIN EQUIPMENT e ON e.EquipmentID = ek.EquipmentID
                                                        WHERE k.KitID = :KitID
                                                    ");
            $equipment->bindParam(':KitID', $incident['KitID']);
            $equipment->execute();
            $equipment = $equipment->fetchAll();

            $incident['Persons'] = $persons;
            $incident['Vehicles'] = $vehicles;
            $incident['Equipment'] = $equipment;
        }
        return $incidents;
    }


    //attach addresses and injuries to people
    function attachToPersons ($persons) {
        global $dbq;

        foreach ($persons as &$person) {
            $addresses = $dbq->prepare("SELECT a.AddressID, a.StreetName, a.City, a.State, c.CountryName FROM PERSON p
                                                        INNER JOIN PERSON_ADDRESS pa ON pa.PersonID = p.PersonID
                                                        INNER JOIN ADDRESS a ON a.AddressID = pa.AddressID
                                                        INNER JOIN COUNTRY c ON c.CountryID = a.CountryID

                                                        WHERE p.PersonID =:PersonID
                                                    ");
            $addresses->bindParam(':PersonID', $person['PersonID']);
            $addresses->execute();
            $addresses = $addresses->fetchAll();

            $injuries = $dbq->prepare("SELECT inj.InjuryID, inj.InjuryName, inj.InjuryDesc, injt.InjuryTypeName, s.SeverityName, s.SeverityLevel FROM PERSON p
                                                        INNER JOIN PERSON_INJURY pinj ON pinj.PersonID = p.PersonID
                                                        INNER JOIN INJURY inj ON pinj.InjuryID = inj.InjuryID
                                                        INNER JOIN SEVERITY s ON pinj.SeverityID = s.SeverityID
                                                        LEFT JOIN INJURY_TYPE injt ON inj.InjuryTypeID = injt.InjuryTypeID

                                                        WHERE p.PersonID = :PersonID
                                                    ");
            $injuries->bindParam(':PersonID', $person['PersonID']);
            $injuries->execute();
            $injuries = $injuries->fetchAll();

            $person['Addresses'] = $addresses;
            $person['Injuries'] = $injuries;
        }

        return $persons;
    }

    //attaches addresses to vehicles
    function attachToVehicles ($vehicles) {
        global $dbq;

        foreach($vehicles as &$vehicle) {
            $addresses = $dbq->prepare("SELECT * FROM VEHICLE v
                                                        INNER JOIN VEHICLE_ADDRESS va ON va.VehicleID = v.VehicleID
                                                        INNER JOIN ADDRESS a ON a.AddressID = va.AddressID
                                                        INNER JOIN COUNTRY c ON c.CountryID = a.CountryID

                                                        WHERE v.VehicleID = :VehicleID
                                                    ");
            $addresses->bindParam(':VehicleID', $vehicle['VehicleID']);
            $addresses->execute();
            $addresses = $addresses->fetchAll();
            $vehicle['Addresses'] = $addresses;
        }

        return $vehicles;
    }

?>