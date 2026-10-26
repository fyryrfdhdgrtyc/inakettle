<?php


    session_start();
    if (!isset($_SESSION['uid'])) {
        header("Location: /loginpage.php");
        exit();
    }
    $db = new SQLite3('inakettle.db');

    $uid = $_SESSION['uid'];
    $pid = $_SESSION['pid'];
    $data = $db->prepare("select * from User where id = :id");
    $data->bindValue(":id", $uid, SQLITE3_INTEGER);
    $res = $data->execute();
    $udata = $res->fetchArray(SQLITE3_ASSOC);

    $taskscountquery = $db->prepare("select count(*) from Task where tuid = :uid");
    $taskscountquery->bindValue(":uid", $uid, SQLITE3_INTEGER);

    $taskscount = $taskscountquery->execute();
    $tcount = $taskscount->fetchArray(SQLITE3_ASSOC);

    $tasksinfoquery = $db->prepare("select id, name, description, status, createdat, priority, tuid, deadline, pid from Task where tuid = :uid order by priority desc");
    $tasksinfoquery->bindValue(":uid", $uid, SQLITE3_INTEGER);

    $projectsquery = $db->prepare("select * from Project where uid = :uid");
    $projectsquery->bindValue(":uid", $uid, SQLITE3_INTEGER);
    $projects = $projectsquery->execute();

    $tasksinforaw = $tasksinfoquery->execute();

    $time = (int)date('H');

    $statuschangequery = $db->prepare('update Task set status = 4 where id = :id');

    while ($task = $tasksinforaw->fetchArray(SQLITE3_ASSOC)):
        if (date($task['deadline']) < date("Y-m-d") && $task['status'] != 3){
            $statuschangequery->bindValue(":id", $task["id"], SQLITE3_INTEGER);
            $statuschangequery->execute();
        }
    
    endwhile;
    
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">

    <title>Your tasks</title>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <header>
        <nav class="navbar sm:navbar-expand">
            <div class="container-fluid">
                <h2><?php echo "Good "; if($time >= 0 && $time <= 4){echo 'night, ';} elseif($time >= 4 && $time <= 11){echo 'morning, ';} elseif($time >= 11 && $time <= 16){echo 'afternoon, ';} elseif($time >= 16 && $time <= 24){echo 'evening, ';} echo $udata['nickname'];?>. <?php echo $tcount['count(*)']; ?> tasks so far</h2>
                  <div class="d-flex" style="gap: 10px;">

                    <a class="btn btn-primary rounded-start m-0"  role="button" data-bs-toggle="modal" data-bs-target="#newProject">New project</a>
                    <form action="newproject.php" method="post">    
                        <div class="modal fade" id="newProject" tabindex="-1" aria-labelledby="newtask" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Create a new project</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                                
                                        <input type="text" class="form-control" placeholder="name" required name="name">
                                                                               
                                    </div>
                                    <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary-outline" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Create</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                      <form action="projectupdate.php" method="post">
                          <select class="form-select" aria-label="Default select example" name="pid" onchange="this.form.submit()">
                                <?php while($project = $projects->fetchArray(SQLITE3_ASSOC)):?>
                                    <?php if($pid == 0): $_SESSION['pid'] = $project['id'];  echo '<script type="text/javascript">window.location.reload();</script>';
    exit();?>
                                    <?php elseif($project['uid'] == $uid):?>
                                        <option value="<?php echo $project['id']?>" <?php if($pid == $project['id']){echo "selected";}?>><?php echo $project['name']?></option>
                                    <?php endif; ?>
                                <?php endwhile; ?>
                            </select>
                      </form>
                            
                     
                    
                <div class="btn-group" role="group">
                    <a class="btn btn-primary rounded-start m-0"  role="button" data-bs-toggle="modal" data-bs-target="#newTask"><i class="fa fa-plus" aria-hidden="true"></i></a>
                    <form action="newtask.php" method="post">    
                        <div class="modal fade" id="newTask" tabindex="-1" aria-labelledby="newtask" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Create a new task</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                                
                                                    <input type="text" class="form-control" placeholder="name" required name="name">
                                                    <input type="text" class="form-control" placeholder="description" name="desc">
                                                    <input type="date" class="form-control" placeholder="deadline" name="deadline" value="<?php echo date("Y-m-d");?>">
                                                    <input type="hidden" name="pid" value="<?php echo $pid?>">                                   
                                                    <div class="d-flex justify-content-between">
                                                        
                                                        <div class="form-range align-items-center">
                                                            <label for="range1" class="form-label">Priority</label>
                                                            <input type="range" class="form-range-input" id="range1" min="1" max="3" step="1" name="priority" value="3" list="rangeTicksList">
                                                            
                                                            <datalist id="rangeTicksList">
                                                              <option value="1" label="low"></option>
                                                              <option value="2"></option>
                                                              <option value="3" label="high"></option>
                                                            </datalist>
                                                        </div>
                                                    </div>
                    
                                                                                    
                                    </div>
                                    <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary-outline" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Create</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <button class="btn btn-secondary-outline rounded-end m-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon" aria-hidden="true"></span>
                </button>
                <dialog class="offcanvas offcanvas-end" tabindex="-1" id="navbarNav" aria-labelledby="navbarNavLabel">
                    <div class="drawer-header d-flex justify-content-between">
                        <h5 class="drawer-title" id="navbarNavLabel">Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="drawer-body">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link active" href="#CNCollapsed" data-bs-toggle="collapse" aria-expanded="false">Cange nickname</a>
                                <div class="collapse"  id="CNCollapsed">
                                    <form action="changenickname.php" method="post"><input type="text" placeholder="new nickname" name="name" required><input type="submit"></form>
                                </div>
                            </li>
                            <li class="nav-item"> 
                                    <a class="nav-link active" name="logout" type="submit" href="logout.php" method="post">Byebye</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" type="submit" href="deleteaccount.php" method="post">Sybau kys</a>
                            </li>
                        </ul>
                    </div>
                </dialog>
                </div>  
                      
                  </div>
            </div>
        </nav>
    </header>
    
    <main style="padding: 0.5%">

    <div class="d-flex justify-content-center">
        <div class="border rounded d-flex flex-column align-items-center" style="width: 25%; min-height: 500px" data-status="todo"><h5 style="text-align: center;">Todo</h5>
            <?php 

                while ($task = $tasksinforaw->fetchArray(SQLITE3_ASSOC)): 
                ?>
                <?php
                if($task['status'] == 1 && $task['pid'] == $pid):?>
                    <div class="card" style="margin: 2%; width: 98%" draggable="true"  data-id="<?php echo htmlspecialchars($task['id']); ?>" style="margin: 2%; width: 96%; cursor: grab;">
                        <div class="card-body">
                            
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <h3 class="card-title text-truncate d-block"><?php echo $task['name']?></h3>
                                <span class="badge <?php  if($task['priority'] == 1){echo "badge text-bg-primary";}elseif($task['priority'] == 2){echo "badge text-bg-success";}elseif($task['priority'] == 3){echo "badge text-bg-danger";}?>"><?php if($task['priority'] == 1){echo "low";} elseif($task['priority'] == 2){echo "medium";} elseif($task['priority'] == 3){echo "high";}?></span>
                                
                                
                            </div>
                            <p class="card-subtitle" style="color: grey"><?php echo "since ",  $task['createdat']?></p>
                            <p class="card-subtitle" style="color: grey"><?php echo "until ", htmlspecialchars((string)$task['deadline']); ?></p>
                            <p class="card-text"><?php echo $task['description']?></p>
                            <div class="d-flex justify-content-between align-items-center" style="width: 100%">
                                <form action="removetask.php" method="post"><button type="submit" class="nav-link active" name="tid" value=<?php echo htmlspecialchars($task['id']); ?> style="color: red;">remove</button></form>
                                <form action="statuschange.php" method="post">
                                    <div class="rounded-pill btn-group" role="group">
                                        <input type="hidden" name="tid" value="<?php echo $task['id']?>">
                                        <button type="submit" class="btn btn-outline-primary rounded-start" name="action" value="down"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
                                        <button type="submit" class="btn btn-outline-primary roundad-end" name="action" value="up"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                                    </div>
                                </form>
                            </div>

                            
                        
                        </div>
                </div>
                    <?php endif;?>
            <?php endwhile; ?>
        </div>

         <div class="border rounded d-flex flex-column align-items-center" style="width: 25%; min-height: 500px;" ><h5 style="text-align: center;">Working</h5>
            <?php 

            while ($task = $tasksinforaw->fetchArray(SQLITE3_ASSOC)): 
                ?>
                <?php
                if($task['status'] == 2 && $task['pid'] == $pid):?>
                    <div class="card" style="margin: 2%; width: 98%" draggable="true"  data-id="<?php echo htmlspecialchars($task['id']); ?>" style="margin: 2%; width: 96%; cursor: grab;">
                        <div class="card-body">
                            
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <h3 class="card-title text-truncate d-block"><?php echo $task['name']?></h3>
                                <span class="badge <?php  if($task['priority'] == 1){echo "badge text-bg-primary";}elseif($task['priority'] == 2){echo "badge text-bg-success";}elseif($task['priority'] == 3){echo "badge text-bg-danger";}?>"><?php if($task['priority'] == 1){echo "low";} elseif($task['priority'] == 2){echo "medium";} elseif($task['priority'] == 3){echo "high";}?></span>
                                
                                
                            </div>
                            <p class="card-subtitle" style="color: grey"><?php echo "since ",  $task['createdat']?></p>
                            <p class="card-subtitle" style="color: grey"><?php echo "until ", htmlspecialchars((string)$task['deadline']); ?></p>
                            <p class="card-text"><?php echo $task['description']?></p>
                            <div class="d-flex justify-content-between align-items-center" style="width: 100%">
                                <form action="removetask.php" method="post"><button type="submit" class="nav-link active" name="tid" value=<?php echo htmlspecialchars($task['id']); ?> style="color: red;">remove</button></form>
                                <form action="statuschange.php" method="post">
                                    <div class="rounded-pill btn-group" role="group">
                                        <input type="hidden" name="tid" value="<?php echo $task['id']?>">
                                        <button type="submit" class="btn btn-outline-primary rounded-start" name="action" value="down"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
                                        <button type="submit" class="btn btn-outline-primary roundad-end" name="action" value="up"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                                    </div>
                                </form>
                            </div>

                            
                        
                        </div>
                </div>
                    <?php endif;?>
            <?php endwhile; ?>
        </div>

            <div class="border rounded d-flex flex-column align-items-center" style="width: 25%; min-height: 500px;" ><h5 style="text-align: center;">Done</h5>
            <?php 

                while ($task = $tasksinforaw->fetchArray(SQLITE3_ASSOC)): 
                ?>
                <?php
                if($task['status'] == 3 && $task['pid'] == $pid):?>
                    <div class="card" style="margin: 2%; width: 98%" draggable="true"  data-id="<?php echo htmlspecialchars($task['id']); ?>" style="margin: 2%; width: 96%; cursor: grab;">
                        <div class="card-body">
                            
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <h3 class="card-title text-truncate d-block"><?php echo $task['name']?></h3>
                                <span class="badge <?php  if($task['priority'] == 1){echo "badge text-bg-primary";}elseif($task['priority'] == 2){echo "badge text-bg-success";}elseif($task['priority'] == 3){echo "badge text-bg-danger";}?>"><?php if($task['priority'] == 1){echo "low";} elseif($task['priority'] == 2){echo "medium";} elseif($task['priority'] == 3){echo "high";}?></span>
                                
                                
                            </div>
                            <p class="card-subtitle" style="color: grey"><?php echo "since ",  $task['createdat']?></p>
                            <p class="card-subtitle" style="color: grey"><?php echo "until ", htmlspecialchars((string)$task['deadline']); ?></p>
                            <p class="card-text"><?php echo $task['description']?></p>
                            <div class="d-flex justify-content-between align-items-center" style="width: 100%">
                                <form action="removetask.php" method="post"><button type="submit" class="nav-link active" name="tid" value=<?php echo htmlspecialchars($task['id']); ?> style="color: red;">remove</button></form>
                                <form action="statuschange.php" method="post">
                                    <div class="rounded-pill btn-group" role="group">
                                        <input type="hidden" name="tid" value="<?php echo $task['id']?>">
                                        <button type="submit" class="btn btn-outline-primary rounded-start" name="action" value="down"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
                                        <button type="submit" class="btn btn-outline-primary roundad-end" name="action" value="up"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                                    </div>
                                </form>
                            </div>

                            
                        
                        </div>
                </div>
                    <?php endif;?>
            <?php endwhile; ?>
        </div>

        <div class="border rounded d-flex flex-column align-items-center" style="width: 25%; min-height: 500px" data-status="todo"><h5 style="text-align: center;">Missed</h5>
            <?php 

                while ($task = $tasksinforaw->fetchArray(SQLITE3_ASSOC)): 
                ?>
                <?php
                if($task['status'] == 4  && $task['pid'] == $pid):?>
                    <div class="card" style="margin: 2%; width: 98%" draggable="true"  data-id="<?php echo htmlspecialchars($task['id']); ?>" style="margin: 2%; width: 96%; cursor: grab;">
                        <div class="card-body">
                            
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <h3 class="card-title text-truncate d-block"><?php echo $task['name']?></h3>
                                <span class="badge <?php  if($task['priority'] == 1){echo "badge text-bg-primary";}elseif($task['priority'] == 2){echo "badge text-bg-success";}elseif($task['priority'] == 3){echo "badge text-bg-danger";}?>"><?php if($task['priority'] == 1){echo "low";} elseif($task['priority'] == 2){echo "medium";} elseif($task['priority'] == 3){echo "high";}?></span>
                                
                                
                            </div>
                            <p class="card-subtitle" style="color: grey"><?php echo "since ",  $task['createdat']?></p>
                            <p class="card-subtitle" style="color: grey"><?php echo "until ", htmlspecialchars((string)$task['deadline']); ?></p>
                            <p class="card-text"><?php echo $task['description']?></p>
                            <div class="d-flex justify-content-between align-items-center" style="width: 100%">
                                <form action="removetask.php" method="post"><button type="submit" class="nav-link active" name="tid" value=<?php echo htmlspecialchars($task['id']); ?> style="color: red;">remove</button></form>
                            </div>

                            
                        
                        </div>
                </div>
                    <?php endif;?>
            <?php endwhile; ?>
        </div>

        
    </div>

    <div class="d-flex justify-content-end" style="margin-top: 10px">
        <form action="deleteproject.php" method="post">
            <button class="btn btn-danger" type="submit" name="pid" value="<?php echo $pid?>">Delete project</button>
        </form>
    </div>
    
    </main>

    </body>
</html>