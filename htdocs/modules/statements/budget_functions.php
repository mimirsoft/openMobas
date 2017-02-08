<?

class budget {

public static function save_budget($id, $start, $end, $account, $monthly, $yearly, $lines)
{
    // we need to delete all existing budget lines for this budget
    $dbh = new DB_Mysql();
    if($id == "NULL")// If it is a new entry.
    {
        $stmt = $dbh->prepare("INSERT INTO statements_budgetlines
                                       SET budgetline_id=:1:, 
                                           start_date=:2:, 
                                           end_date=:3:, 
                                           budget_account=:4:, 
                                           budget_monthly=:5:, 
                                           budget_yearly=:6:");
    }
    else//If it is an edit to an existing entry
    {
        $stmt = $dbh->prepare("UPDATE statements_budgetlines 
                                       SET budget_id
                                           budget_account=:4:, 
                                           budget_monthly=:5:, 
                                           budget_yearly=:6:
                                     WHERE budgetline_id=:1:");
    }
    $stmt->execute($id, $start, $end, $account, $monthly, $yearly);
}

public static function getall_budgetlines()
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT * FROM statements_budget ORDER BY end_date");
    $stmt->execute();	
    return  $stmt->fetchall_assoc();
}

}

?>