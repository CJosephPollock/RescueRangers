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
        $toCall = $_GET['f'];
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
            'LocationLat' => $_POST['Latitude'],
            'LocationLng' => $_POST['Longitude'],
            'StreetName' => $_POST['StreetName'],
            'City' => $_POST['City'],
            'State' => $_POST['State'],
            'Country' => $_POST['Country']
        ];

        $sth = $dbh->prepare("exec startIncident
                                            @IncidentName=:IncidentName
                                            @IncidentDesc=:IncidentDesc
                                            @IncidentTypeName=:IncidentTypeName
                                            @KitName=:KitName
                                            @FirstName =:FirstName
                                            @LastName =:LastName
                                            @SeverityName =:SeverityName
                                            @InjuryName =:InjuryName
                                            @InjuryDesc =:InjuryDesc
                                            @InjuryTypeName =:InjuryTypeName
                                            @LocationName =:LocationName
                                            @LocationLat =:LocationLat
                                            @LocationLng =:LocationLng
                                            @StreetName =:StreetName
                                            @City =:City
                                            @State =:State
                                            @Country =:Country");
        $sth->bindParam(':IncidentName', $data['IncidentName']);
        $sth->bindParam(':IncidentDesc', $data['IncidentDesc']);
        $sth->bindParam(':IncidentTypeName', $data['IncidentTypeName']);

        $sth->bindParam(':KitName', $data['KitName']);
        $sth->bindParam(':FirstName', $data['FirstName']);
        $sth->bindParam(':LastName', $data['LastName']);

        $sth->bindParam(':SeverityName', $data['SeverityName']);

        $sth->bindParam(':InjuryName', $data['InjuryName']);
        $sth->bindParam(':InjuryDesc', $data['InjuryDesc']);
        $sth->bindParam(':InjuryTypeName', $data['InjuryTypeName']);

        $sth->bindParam(':LocationName', $data['LocationName']);
        $sth->bindParam(':LocationLat', $data['LocationLat']);
        $sth->bindParam(':LocationLng', $data['LocationLng']);

        $sth->bindParam(':StreetName', $data['StreetName']);
        $sth->bindParam(':City', $data['City']);
        $sth->bindParam(':State', $data['State']);
        $sth->bindParam(':Country', $data['Country']);

        $sth->execute();
    }

    function addIncident ($IncidentName, $IncidentDesc, $KitName, $LocationLat, $IncidentTypeName) {
        global $dbq;
        $sth = $dbh->prepare("exec popIncident @IncidentName=:IncidentName @IncidentDesc=:IncidentDesc @IncidentDateCreated=:IncidentDateCreated @KitName=:KitName @LocationLat=:locationLat @IncidentTypeName=:IncidentTypeName");
        $sth->bindParam(':IncidentName', $IncidentName);
        $sth->bindParam(':IncidentDesc', $IncidentDesc);
        $sth->bindParam(':IncidentDateCreated', date("Y-m-d H:m:s"));
        $sth->bindParam(':KitName', $KitName);
        $sth->bindParam(':LocationLat', $LocationLat);
        $sth->bindParam(':IncidentTypeName', $IncidentTypeName);
        $sth->execute();
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