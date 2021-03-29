<?php

class Archer extends Personnage{
    
    public function frapper(Personnage $perso){
        
        if ($perso->id() == $this->_id){
            return self::CEST_MOI;
        }
        var_dump($perso->type());
        if ($perso->type() == 'guerrier') {
           return $perso->recevoirDegats(2);
        } else {
           return $perso->recevoirDegats(1);
            
        }
        
        
        // On indique au personnage qu'il doit recevoir des dégâts.
        // Puis on retourne la valeur renvoyée par la méthode : self::PERSONNAGE_TUE ou self::PERSONNAGE_FRAPPE
    
    }
    
    

}