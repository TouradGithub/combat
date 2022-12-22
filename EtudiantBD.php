<?php
class EtudiantBD 
{
    private $_db; 
     
    public function __construct($db)
    {
        $this->setDb($db);
    }
     
    public function add(Etudiants $Etud)
    {
        $q = $this->_db->prepare('INSERT INTO Etudiants (nom) VALUES (:nom)');
        $q->bindValue(':nom', $Etud->nom());
        $q->execute();
         
        
         
        $Etud->hydrate([
            'id'=>$this->_db->lastInsertId(),
            'degats' => 0,
            'experience' => 0,
            'niveau' => 1,
            'nbfrap' => 0
            ]);
    }
     
    public function count()
    {
        return $this->_db->query('SELECT COUNT(*) FROM Etudiants')->fetchColumn();
    }
     
    public function delete(Etudiants $Etud)
    {
        $this->_db->exec('DELETE FROM Etudiants WHERE id = '.$Etud->id());
    }
     
    public function exists($info)
    {
        if (is_int($info))
        {
            return (bool)$this->_db->query('SELECT COUNT(*) FROM Etudiants WHERE id = '.$info)->fetchColumn();
        }
         
        $q = $this->_db->prepare('SELECT COUNT(*) FROM Etudiants WHERE nom = :nom');
        $q -> execute([':nom' => $info]);
         
        return (bool) $q->fetchColumn();
    }
     
    public function get($info)
    {
        if (is_int($info))
        {
            $q = $this->_db->query('SELECT id, nom, degats, experience, niveau, nbfrap FROM Etudiants WHERE id = '.$info);
            $donnees = $q->fetch(PDO::FETCH_ASSOC);
             
            return new Etudiants($donnees);
        }
         
        $q = $this -> _db ->prepare('SELECT id, nom, degats, experience, niveau, nbfrap FROM Etudiants WHERE nom = :nom');
        $q->execute([':nom' => $info]);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
         
        return new Etudiants($donnees);
    }
     
    public function getList($nom)
    {
        $Etudiant = [];
 
        $q  =  $this->_db->prepare('SELECT id, nom, degats, experience, niveau, nbfrap FROM Etudiants WHERE nom <> :nom ORDER BY nom');
        $q->execute([':nom'=>$nom]);
 
        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {  
            $Etudiant[] = new Etudiants($donnees);
        }
        return $Etudiant;
    }
     
    public function update(Etudiants $Etud)
    {
        $q  =  $this->_db->prepare('UPDATE Etudiants SET degats = :degats, experience = :experience, niveau = :niveau, nbfrap = :nbfrap WHERE id = :id');
        $q->bindValue(':degats',$Etud->degats(), PDO::PARAM_INT);
        $q->bindValue(':experience',$Etud->experience(), PDO::PARAM_INT);
        $q->bindValue(':niveau',$Etud->niveau(), PDO::PARAM_INT);
        $q->bindValue(':nbfrap',$Etud->nbfrap(), PDO::PARAM_INT);
        $q->bindValue(':id',$Etud->id(), PDO::PARAM_INT);
        $q->execute();
    }
     
    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
     
}