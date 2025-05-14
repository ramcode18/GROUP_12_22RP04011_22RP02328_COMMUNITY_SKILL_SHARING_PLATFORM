<?php
// menu.php
require_once 'sms.php';

class Menu {
    protected $phone;
    protected $db;
    public $sms;

    public function __construct($phone) {
        $this->phone = $phone;
        $this->sms   = new Sms($phone);

        try {
            // Adjust host/user/pass/dbname as needed
            $this->db = new PDO("mysql:host=localhost;dbname=skillshare", "root", "");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("DB connection error: " . $e->getMessage());
            $this->sendEnd("An error occurred while connecting to the database.");
        }
    }

    // ===== Helpers =====
    protected function sendCon(string $msg) {
        echo "CON $msg";
    }

    protected function sendEnd(string $msg) {
        echo "END $msg";
        // send SMS without the "END " prefix
        $this->sms->sendSMS($msg, $this->phone);
        exit;
    }

    // ===== Unregistered =====
    public function mainMenuUnregistered() {
        $this->sendCon("Welcome To Skill Sharing Platform\n1. Register");
    }

    public function menuRegister(array $parts) {
        $level = count($parts);
        switch ($level) {
            case 1:
                $this->sendCon("Enter your Full Name:");
                break;
            case 2:
                $this->sendCon("Enter your Email Address:");
                break;
            case 3:
                $this->sendCon("Enter your Phone Number:");
                break;
            case 4:
                $this->sendCon("Select your Role:\n1. Admin\n2. User");
                break;
            case 5:
                $this->sendCon("Enter your Password:");
                break;
            case 6:
                $this->sendCon("Re-enter your Password:");
                break;
            case 7:
                $pwd = $parts[5] ?? '';
                $cpw = $parts[6] ?? '';
                if ($pwd !== $cpw) {
                    $this->sendEnd("Passwords do not match. Please restart the registration.");
                }
                $name  = $parts[1];
                $email = $parts[2];
                $phone = $parts[3];
                $role  = $parts[4]==="1"?"admin":"user";
                $this->sendCon(
                    "Confirm your registration:\n".
                    "Name: $name\n".
                    "Email: $email\n".
                    "Phone: $phone\n".
                    "Role: $role\n\n".
                    "1. Confirm\n2. Cancel"
                );
                break;
            case 8:
                if (($parts[7] ?? '') !== "1") {
                    $this->sendEnd("Registration Cancelled.");
                }
                // gather
                $name  = trim($parts[1]);
                $email = trim($parts[2]);
                $phone = trim($parts[3]);
                $role  = $parts[4]==="1"?"admin":"user";
                $pwd   = password_hash($parts[5], PASSWORD_BCRYPT);
                // validations
                if (!$name||!$email||!$phone) {
                    $this->sendEnd("Dear $name, Registration failed: All fields are required.");
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->sendEnd("Dear $name, Registration failed: Invalid email format.");
                }
                if (!preg_match('/^[0-9]{10,15}$/',$phone)) {
                    $this->sendEnd("Dear $name, Registration failed: Invalid phone number.");
                }
                try {
                    // duplicates
                    $chk = $this->db->prepare("SELECT COUNT(*) FROM users WHERE phone=? OR email=?");
                    $chk->execute([$phone,$email]);
                    if ($chk->fetchColumn()>0) {
                        $this->sendEnd("Dear $name, Registration failed: Phone or email already registered.");
                    }
                    // insert
                    $ins = $this->db->prepare(
                        "INSERT INTO users(full_name,email,phone,role,password,registered_via)
                         VALUES(?,?,?,?,?, 'ussd')"
                    );
                    $ins->execute([$name,$email,$phone,$role,$pwd]);
                    $this->sendEnd("Dear $name, you have successfully registered as $role.");
                } catch (PDOException $e) {
                    error_log("Registration error: ".$e->getMessage());
                    $this->sendEnd("Registration failed. Please try again later.");
                }
                break;
            default:
                $this->sendEnd("Invalid option. Please try again.");
        }
    }

    // ===== Registered Main Menu =====
    public function mainMenuRegistered() {
        try {
            $stmt = $this->db->prepare("SELECT id,role,full_name FROM users WHERE phone=?");
            $stmt->execute([$this->phone]);
            $u = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$u) {
                $this->sendEnd("User not found. Please register first.");
            }
            $name = $u['full_name'];
            $role = $u['role'];
            if ($role === 'admin') {
                $menu = "Welcome $name\n".
                        "1. Upload Lesson\n".
                        "2. Update Lesson\n".
                        "3. View All Lessons\n".
                        "4. Delete Lesson\n".
                        "5. View My Lessons\n".
                        "6. Approve Lessons\n".
                        "7. View Requested Lessons\n".
                        "8. Monthly Payment\n".
                        "0. Exit\n00. Main Menu";
                $this->sendCon($menu);
            } else {
                $menu = "Welcome $name\n".
                        "1. Request New Lesson\n".
                        "2. Browse Available Lessons\n".
                        "3. My Requested Lessons\n".
                        "4. Cancel Lesson Request\n".
                        "0. Exit\n00. Main Menu";
                $this->sendCon($menu);
            }
        } catch (PDOException $e) {
            error_log("Menu error: ".$e->getMessage());
            $this->sendEnd("An error occurred. Please try again later.");
        }
    }

    // ===== Admin Flows =====
    public function menuUploadLesson(array $p) {
        $lvl = count($p);
        // admin id
        $stmt = $this->db->prepare("SELECT id FROM users WHERE phone=?");
        $stmt->execute([$this->phone]);
        $adm = $stmt->fetchColumn();
        if (!$adm) {
            $this->sendEnd("User not found. Please register first.");
        }
        switch ($lvl) {
            case 1:
                $this->sendCon("Enter Lesson Title:");
                break;
            case 2:
                $this->sendCon("Enter Lesson Description:");
                break;
            case 3:
                $this->sendCon("Enter Lesson Content:");
                break;
            case 4:
                $this->sendCon("Enter Lesson Duration (minutes):");
                break;
            case 5:
                $this->sendCon("Enter Lesson Category:\n1. Programming\n2. Design\n3. Business\n4. Other");
                break;
            case 6:
                $title = trim($p[1]); $desc=trim($p[2]); $cont=trim($p[3]); $dur=trim($p[4]); $cat=$p[5];
                if (!$title||!$desc||!$cont||!$dur) {
                    $this->sendEnd("All fields are required. Please try again.");
                }
                if (!is_numeric($dur)||$dur<=0) {
                    $this->sendEnd("Invalid duration. Please enter a positive number.");
                }
                $map = ['1'=>'Programming','2'=>'Design','3'=>'Business','4'=>'Other'];
                $cat = $map[$cat] ?? 'Other';
                try {
                    $ins = $this->db->prepare(
                        "INSERT INTO lessons(admin_id,title,content,is_approved)
                         VALUES(?,?,?,1)"
                    );
                    $ins->execute([$adm,$title,$cont]);
                    $this->sendEnd("Lesson uploaded successfully!");
                } catch (PDOException $e) {
                    error_log("Upload error: ".$e->getMessage());
                    $this->sendEnd("Failed to upload lesson. Please try again.");
                }
                break;
            default:
                $this->sendEnd("Invalid option. Please try again.");
        }
    }

    public function menuUpdateLesson(array $p) {
        $lvl = count($p);
        $stmt = $this->db->prepare("SELECT id FROM users WHERE phone=?");
        $stmt->execute([$this->phone]);
        $adm = $stmt->fetchColumn();
        if (!$adm) {
            $this->sendEnd("User not found. Please register first.");
        }
        switch ($lvl) {
            case 1:
                try {
                    $q = $this->db->prepare("SELECT id,title FROM lessons WHERE admin_id=?");
                    $q->execute([$adm]);
                    $less = $q->fetchAll(PDO::FETCH_ASSOC);
                    if (!$less) {
                        $this->sendEnd("You have no lessons to update.");
                    }
                    $resp = "Select lesson to update:\n";
                    foreach ($less as $l) {
                        $resp .= "{$l['id']}. {$l['title']}\n";
                    }
                    $this->sendCon(trim($resp));
                } catch (PDOException $e) {
                    error_log("Fetch error: ".$e->getMessage());
                    $this->sendEnd("An error occurred. Please try again.");
                }
                break;
            case 2:
                $lid = $p[1];
                try {
                    $q = $this->db->prepare("SELECT * FROM lessons WHERE id=? AND admin_id=?");
                    $q->execute([$lid,$adm]);
                    $l = $q->fetch(PDO::FETCH_ASSOC);
                    if (!$l) {
                        $this->sendEnd("Lesson not found.");
                    }
                    $this->sendCon("Select field to update:\n1. Title\n2. Description\n3. Content\n4. Duration\n5. Category");
                } catch (PDOException $e) {
                    error_log("Fetch error: ".$e->getMessage());
                    $this->sendEnd("An error occurred. Please try again.");
                }
                break;
            case 3:
                $lid = $p[1]; $f=$p[2];
                $map=['1'=>'title','2'=>'description','3'=>'content','4'=>'duration','5'=>'category'];
                if (!isset($map[$f])) {
                    $this->sendEnd("Invalid field selection.");
                }
                $this->sendCon("Enter new ".$map[$f].":");
                break;
            case 4:
                $lid = $p[1]; $f=$p[2]; $nv=trim($p[3]);
                if ($nv==='') {
                    $this->sendEnd("Value cannot be empty. Please try again.");
                }
                $map=['1'=>'title','2'=>'description','3'=>'content','4'=>'duration','5'=>'category'];
                try {
                    $u = $this->db->prepare("UPDATE lessons SET {$map[$f]}=? WHERE id=? AND admin_id=?");
                    $u->execute([$nv,$lid,$adm]);
                    if ($u->rowCount()>0) {
                        $this->sendEnd("Lesson updated successfully!");
                    } else {
                        $this->sendEnd("Failed to update lesson.");
                    }
                } catch (PDOException $e) {
                    error_log("Update error: ".$e->getMessage());
                    $this->sendEnd("An error occurred. Please try again.");
                }
                break;
            default:
                $this->sendEnd("Invalid option. Please try again.");
        }
    }

    public function menuViewLesson(array $p) {
        $lvl = count($p);
        switch ($lvl) {
            case 1:
                try {
                    $q = $this->db->query("SELECT id,title FROM lessons WHERE is_approved=1");
                    $ls = $q->fetchAll(PDO::FETCH_ASSOC);
                    if (!$ls) {
                        $this->sendEnd("No lessons available.");
                    }
                    $resp = "Available Lessons:\n";
                    foreach ($ls as $l) {
                        $resp .= "{$l['id']}. {$l['title']}\n";
                    }
                    $this->sendCon(trim($resp));
                } catch (PDOException $e) {
                    error_log("Fetch error: ".$e->getMessage());
                    $this->sendEnd("An error occurred. Please try again.");
                }
                break;
            case 2:
                $lid = $p[1];
                try {
                    $q = $this->db->prepare("SELECT title,content FROM lessons WHERE id=? AND is_approved=1");
                    $q->execute([$lid]);
                    $l = $q->fetch(PDO::FETCH_ASSOC);
                    if (!$l) {
                        $this->sendEnd("Lesson not found.");
                    }
                    $this->sendCon("Title: {$l['title']}\nContent: {$l['content']}");
                } catch (PDOException $e) {
                    error_log("Fetch error: ".$e->getMessage());
                    $this->sendEnd("An error occurred. Please try again.");
                }
                break;
            default:
                $this->sendEnd("Invalid option. Please try again.");
        }
    }

    public function menuDeleteLesson(array $p) {
        $lvl = count($p);
        // admin id
        $stmt = $this->db->prepare("SELECT id FROM users WHERE phone=?");
        $stmt->execute([$this->phone]);
        $adm = $stmt->fetchColumn();
        if (!$adm) {
            $this->sendEnd("User not found. Please register first.");
        }
        switch ($lvl) {
            case 1:
                try {
                    $q = $this->db->query("SELECT id,title FROM lessons");
                    $ls = $q->fetchAll(PDO::FETCH_ASSOC);
                    if (!$ls) {
                        $this->sendEnd("No lessons available to delete.");
                    }
                    $resp = "Select lesson to delete:\n";
                    foreach ($ls as $l) {
                        $resp .= "{$l['id']}. {$l['title']}\n";
                    }
                    $this->sendCon(trim($resp));
                } catch (PDOException $e) {
                    error_log("Fetch error: ".$e->getMessage());
                    $this->sendEnd("An error occurred. Please try again.");
                }
                break;
            case 2:
                $lid = $p[1];
                try {
                    $d = $this->db->prepare("DELETE FROM lessons WHERE id=?");
                    $d->execute([$lid]);
                    if ($d->rowCount()>0) {
                        $this->sendEnd("Lesson deleted successfully!");
                    } else {
                        $this->sendEnd("Failed to delete lesson.");
                    }
                } catch (PDOException $e) {
                    error_log("Delete error: ".$e->getMessage());
                    $this->sendEnd("An error occurred. Please try again.");
                }
                break;
            default:
                $this->sendEnd("Invalid option. Please try again.");
        }
    }

    public function menuApproveLesson(array $p) {
        $lvl = count($p);
        // admin id
        $stmt = $this->db->prepare("SELECT id FROM users WHERE phone=?");
        $stmt->execute([$this->phone]);
        $adm = $stmt->fetchColumn();
        if (!$adm) {
            $this->sendEnd("User not found. Please register first.");
        }
        switch ($lvl) {
            case 1:
                try {
                    $q = $this->db->query("SELECT id,topic,description,user_id FROM lesson_requests WHERE status='pending'");
                    $rs = $q->fetchAll(PDO::FETCH_ASSOC);
                    if (!$rs) {
                        $this->sendEnd("No pending lesson requests.");
                    }
                    $resp = "Pending Requests:\n";
                    foreach ($rs as $r) {
                        $resp .= "{$r['id']}. {$r['topic']}\nDesc: {$r['description']}\nBy User ID: {$r['user_id']}\n\n";
                    }
                    $this->sendCon(trim($resp));
                } catch (PDOException $e) {
                    error_log("Fetch error: ".$e->getMessage());
                    $this->sendEnd("An error occurred. Please try again.");
                }
                break;
            case 2:
                $rid = $p[1];
                try {
                    $u = $this->db->prepare("UPDATE lesson_requests SET status='fulfilled' WHERE id=? AND status='pending'");
                    $u->execute([$rid]);
                    if ($u->rowCount()>0) {
                        $this->sendEnd("Lesson request approved successfully!");
                    } else {
                        $this->sendEnd("Failed to approve request.");
                    }
                } catch (PDOException $e) {
                    error_log("Approve error: ".$e->getMessage());
                    $this->sendEnd("An error occurred. Please try again.");
                }
                break;
            default:
                $this->sendEnd("Invalid option. Please try again.");
        }
    }

    // ===== User Flows =====
    public function menuRequestLesson(array $p) {
        $lvl = count($p);
        // user id
        $stmt = $this->db->prepare("SELECT id FROM users WHERE phone=?");
        $stmt->execute([$this->phone]);
        $uid = $stmt->fetchColumn();
        if (!$uid) {
            $this->sendEnd("User not found. Please register first.");
        }
        switch ($lvl) {
            case 1:
                $this->sendCon("Enter Lesson Topic:");
                break;
            case 2:
                $this->sendCon("Enter Lesson Description:");
                break;
            case 3:
                $t = trim($p[1]); $d = trim($p[2]);
                if (!$t||!$d) {
                    $this->sendEnd("All fields are required. Please try again.");
                }
                try {
                    $i = $this->db->prepare(
                        "INSERT INTO lesson_requests(user_id,topic,description,status)
                         VALUES(?,?,?,'pending')"
                    );
                    $i->execute([$uid,$t,$d]);
                    $this->sendEnd("Lesson request submitted successfully!");
                } catch (PDOException $e) {
                    error_log("Request error: ".$e->getMessage());
                    $this->sendEnd("Failed to submit lesson request. Please try again.");
                }
                break;
            default:
                $this->sendEnd("Invalid option. Please try again.");
        }
    }

    public function menuViewMyLessons(array $p) {
        $lvl = count($p);
        switch ($lvl) {
            case 1:
                $stmt = $this->db->prepare("SELECT id FROM users WHERE phone=?");
                $stmt->execute([$this->phone]);
                $uid = $stmt->fetchColumn();
                if (!$uid) {
                    $this->sendEnd("User not found. Please register first.");
                }
                try {
                    $q = $this->db->prepare(
                        "SELECT id,topic,status,created_at
                         FROM lesson_requests
                         WHERE user_id=?"
                    );
                    $q->execute([$uid]);
                    $rs = $q->fetchAll(PDO::FETCH_ASSOC);
                    if (!$rs) {
                        $this->sendEnd("You have no lesson requests.");
                    }
                    $resp = "Your Requests:\n";
                    foreach ($rs as $r) {
                        $resp .= "ID {$r['id']}: {$r['topic']} ({$r['status']}) on {$r['created_at']}\n\n";
                    }
                    $this->sendCon(trim($resp));
                } catch (PDOException $e) {
                    error_log("Fetch error: ".$e->getMessage());
                    $this->sendEnd("An error occurred. Please try again.");
                }
                break;
            default:
                $this->sendEnd("Invalid option. Please try again.");
        }
    }

    public function menuCancelLesson(array $p) {
        $lvl = count($p);
        $stmt = $this->db->prepare("SELECT id FROM users WHERE phone=?");
        $stmt->execute([$this->phone]);
        $uid = $stmt->fetchColumn();
        if (!$uid) {
            $this->sendEnd("User not found. Please register first.");
        }
        switch ($lvl) {
            case 1:
                try {
                    $q = $this->db->prepare(
                        "SELECT id,topic FROM lesson_requests
                         WHERE user_id=? AND status='pending'"
                    );
                    $q->execute([$uid]);
                    $rs = $q->fetchAll(PDO::FETCH_ASSOC);
                    if (!$rs) {
                        $this->sendEnd("No pending requests to cancel.");
                    }
                    $resp = "Select request to cancel:\n";
                    foreach ($rs as $r) {
                        $resp .= "{$r['id']}. {$r['topic']}\n";
                    }
                    $this->sendCon(trim($resp));
                } catch (PDOException $e) {
                    error_log("Fetch error: ".$e->getMessage());
                    $this->sendEnd("An error occurred. Please try again.");
                }
                break;
            case 2:
                $rid = $p[1];
                try {
                    $d = $this->db->prepare(
                        "DELETE FROM lesson_requests
                         WHERE id=? AND user_id=? AND status='pending'"
                    );
                    $d->execute([$rid,$uid]);
                    if ($d->rowCount()>0) {
                        $this->sendEnd("Lesson request cancelled successfully!");
                    } else {
                        $this->sendEnd("Failed to cancel request.");
                    }
                } catch (PDOException $e) {
                    error_log("Delete error: ".$e->getMessage());
                    $this->sendEnd("An error occurred. Please try again.");
                }
                break;
            default:
                $this->sendEnd("Invalid option. Please try again.");
        }
    }

    public function menuViewRequestedLessons(array $p) {
        $lvl = count($p);
        // admin only
        $stmt = $this->db->prepare("SELECT role FROM users WHERE phone=?");
        $stmt->execute([$this->phone]);
        $r = $stmt->fetchColumn();
        if ($r !== 'admin') {
            $this->sendEnd("Access denied.");
        }
        if ($lvl !== 1) {
            $this->sendEnd("Invalid option.");
        }
        try {
            $q = $this->db->query(
                "SELECT lr.id,lr.topic,lr.description,lr.status,u.phone
                 FROM lesson_requests lr
                 JOIN users u ON lr.user_id=u.id
                 ORDER BY lr.created_at DESC"
            );
            $rs = $q->fetchAll(PDO::FETCH_ASSOC);
            if (!$rs) {
                $this->sendEnd("No lesson requests found.");
            }
            $resp = "All Requested Lessons:\n";
            foreach ($rs as $r) {
                $resp .= "{$r['id']}. {$r['topic']}\n".
                         "Desc: {$r['description']}\n".
                         "Status: {$r['status']}\n".
                         "By: {$r['phone']}\n\n";
            }
            $this->sendCon(trim($resp));
        } catch (PDOException $e) {
            error_log("Fetch error: ".$e->getMessage());
            $this->sendEnd("An error occurred. Please try again.");
        }
    }

    public function menuMonthlyPayment(array $p) {
        $lvl = count($p);
        // admin only
        $stmt = $this->db->prepare("SELECT role FROM users WHERE phone=?");
        $stmt->execute([$this->phone]);
        $r = $stmt->fetchColumn();
        if ($r !== 'admin') {
            $this->sendEnd("Access denied.");
        }
        switch ($lvl) {
            case 1:
                try {
                    $q = $this->db->query(
                        "SELECT id,full_name,phone FROM users WHERE role='user' ORDER BY full_name"
                    );
                    $us = $q->fetchAll(PDO::FETCH_ASSOC);
                    if (!$us) {
                        $this->sendEnd("No users found for payment.");
                    }
                    $resp = "Select user to mark paid this month:\n";
                    foreach ($us as $u) {
                        $resp .= "{$u['id']}. {$u['full_name']} ({$u['phone']})\n";
                    }
                    $this->sendCon(trim($resp));
                } catch (PDOException $e) {
                    error_log("Fetch error: ".$e->getMessage());
                    $this->sendEnd("An error occurred. Please try again.");
                }
                break;
            case 2:
                $uid = $p[1];
                $month = date('Y-m');
                try {
                    $this->db->exec(
                        "CREATE TABLE IF NOT EXISTS payments (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            user_id INT,
                            month VARCHAR(7),
                            paid TINYINT(1) DEFAULT 1,
                            paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            UNIQUE(user_id,month)
                        )"
                    );
                    $i = $this->db->prepare(
                        "INSERT INTO payments(user_id,month,paid)
                         VALUES(?,? ,1)
                         ON DUPLICATE KEY UPDATE paid=1,paid_at=CURRENT_TIMESTAMP"
                    );
                    $i->execute([$uid,$month]);
                    $this->sendEnd("Payment marked complete for user $uid for $month.");
                } catch (PDOException $e) {
                    error_log("Payment error: ".$e->getMessage());
                    $this->sendEnd("Failed to mark payment. Please try again.");
                }
                break;
            default:
                $this->sendEnd("Invalid option. Please try again.");
        }
    }
}
