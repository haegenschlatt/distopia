<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post extends CI_Controller {
	//	Create new thread
	public function _remap() {
		//$this->disfunctions->checkBan();
		
		// Connect to CAPTCHA and verify that the user's challenge was entered correctly. If not, abort the script.
		/* Removed for testing.
		$captcha_data = array(
			'privatekey' => CAPTCHA_PRIVATE_KEY,
			'remoteip' => $_SERVER['REMOTE_ADDR'],
			'challenge' => $this->input->post("recaptcha_challenge_field"),
			'response' => $this->input->post("recaptcha_response_field"),
		);
		$captchaResponse = $this->post_request("http://www.google.com/recaptcha/api/verify", $captcha_data);
		if($captchaResponse["status"]=="ok")
		{
			$captchaResults = explode("\n",$captchaResponse["content"]);
			if($captchaResults[0]=="false")
			{
				exit("You entered the verification incorrectly. <a href='".$this->input->post("origin")."'>Back to the previous page</a>");
			}
		} else
		{
			exit("Cannot connect to CAPTCHA service. Please try again later. <a href='".$this->input->post("origin")."'>Back to the previous page</a>");
		}
		*/

		$auth = $this->Users->getAuth();
		if(!$auth)
		{
			exit("Not logged in!");
		} else
		{
			$username = $auth["username"];
			$userid = $auth["userid"];
		}

		$type = $this->input->post("type");

		if(!($type == "reply" || $type == "thread" || $type == "gallery" || $type == "stream"))
		{
			exit("Error: Invalid post type.");
		}

		$board = $this->input->post('board');

		// Text content is optional for galleries.
		if($this->input->post("content")=="" && $type != "gallery")
		{
			exit("You did not write any content.");
		}

		if(!$this->input->post("title") && $type != "reply")
		{
			exit("You did not include a title!");
		} else
		{
			$title = $this->input->post("title");
		}

		// Because we parse the content for URLs and greentext, we have to sanitize before we do that.
		$content = htmlentities($this->input->post('content'), ENT_QUOTES);
		// Check for and create greentext.
		$content = $this->greenText($content);
		// Check the text for URLs and replace them with links.
		$content = $this->findURLs($content);
		// Lastly, turn each line-break character into a new paragraph. This allows breaks to appear properly.
		$content = "<p>" . str_replace("\n","</p><p>",$content) . "</p>";

		// Now we check for r9k match. Since the content is already parsed and de-injected we can just put $content directly into the query.
		// Since we want to compare it to existing entries in the DB this is what we want to do anyway.
		// At the moment, the r9k match only checks for an EXACT match in the string - case and everything.
		// In the future it should work the way Munroe originally specified it.
		// Check to see if r9k is enabled for this board
		$query = $this->db->query("SELECT r9k FROM boardmeta WHERE name=?",array($board));
		$r9kcheck = $query->row_array();
		$r9k = $r9kcheck["r9k"];
		$query = $this->db->query("SELECT * FROM posts WHERE content=? AND board=?",array($content,$board));
		if($query->num_rows()>0 && $r9k)
		{
			exit("This content is not original. Write something unique! <a href='".$this->input->post("origin")."'>Back to the previous page</a>");
		}

		$date = time();

		//	Since threads and posts are in two different tables, we have to add them.
		$query = $this->db->query("SELECT id FROM posts ORDER BY id DESC LIMIT 1");
		if($query->num_rows() > 0)
		{
			$id = $query->row_array()["id"];
		} else
		{
			$id = 0;
		}
		$query = $this->db->query("SELECT id FROM threads ORDER BY id DESC LIMIT 1");
		if($query->num_rows() > 0)
		{
			$id += $query->row_array()["id"];
		} else
		{
			$id += 0;
		}
		$id++;

		/*
		if($query->num_rows()>0)
		{
			$results = $query->row();
			$id = $results->id;
			$id++;
		} else
		{
			$id=0;
		}
		*/

		//	Thread containing the post. This variable is only used for posts.
		$thread = $this->input->post('thread');
		//	REMOTE_ADDR is modified by Cloudflare.php in application/hooks.
		$ip = $_SERVER["REMOTE_ADDR"];
		//	The board is sent in a hidden field via POST.

		// Make sure the board exists
		$query = $this->db->query("SELECT * FROM boardmeta WHERE name=?;",array($board));
		if($query->num_rows()===1)
		{
			$board = $this->input->post('board');
		} else
		{
			exit("Error!");
		}
		//	If the post is creating a new thread, there will be no parent, so we use -1.
		//	If the post is in reply to the OP, the OP will be the parent.
		//	If the post is in reply to another post, that post will be the parent.
		if($thread == -1)
		{
			// This isn't really necessary - threads have their own table now, with no "parent" field
			$parent = -1;
		} else if($this->input->post('parent')=="")
		{
			$parent = $thread;
		} else
		{
			$parent = $this->input->post('parent');
		}
		//	The "latest" field is used for threads and holds the date of the latest reply posted. This is used for bumping posts to the front page.
		//	A reply post will not need to have a "latest" field. Only a thread post (top-level, OP, $thread is -1) will need to have a latest field.
		//	When a reply post is created, the "latest" field on its thread is updated.
		//	When a thread is created, the "latest" field on that thread post is the date of its creation.
		if($type != "reply")
		{
			$latest = time();
		} else if($this->input->post('sage')!="y")
		{
			$this->db->query("UPDATE threads SET latest = ? WHERE id = ?",array(time(),$thread));
			$latest = time();
		} else if($this->input->post('sage')=="y")
		{
			$query = $this->db->query("SELECT latest FROM threads WHERE id=?;",array($thread));
			$latest = $query->row_array()["latest"];
		}

		// Image handling
		if($_FILES["upload"]["name"] == null)
		{
			$imgEntry = 0;
			if($type == "gallery")
			{
				exit("You must upload an image to create a gallery!");
			}
		} else
		{
			if($_FILES["upload"]["type"] == "image/jpeg")
			{
				$filetype = "jpg";
			} else if($_FILES["upload"]["type"] == "image/png")
			{
				$filetype = "png";
			} else if($_FILES["upload"]["type"] == "image/gif")
			{
				$filetype = "gif";
			} else
			{
				exit("This is not a valid file type.");
			}

			$imgCount = 0;
			$handle = opendir("images");

			while(false !== ($entry = readdir($handle)))
			{
				$imgCount++;
			}
			// Removes two because of ".", "..", and "thumbs" so that the count starts from 1.
			$imgCount-=2;

			move_uploaded_file($_FILES["upload"]["tmp_name"], "images/$imgCount".".".$filetype);
			
			$config["image_library"] = "gd2";
			$config["source_image"] = "images/$imgCount" . "." . $filetype;
			$config["create_thumb"] = FALSE;
			$config["new_image"] = "images/thumbs/$imgCount" . "." . $filetype;
			$config['width'] = 200;
			$config['height'] = 200;

			$this->load->library("image_lib", $config);

			$this->image_lib->resize();

			$imgEntry = $imgCount . "." . $filetype;
		}

		// Put everything into the DB!

		if($type == "reply")
		{
			// Reply to post
			$postContent = array(
				$userid,
				$username,
				$content,
				$date,
				$id,
				$ip,
				$board,
				$thread,
				$parent,
				$imgEntry);
			$query = $this->db->query("INSERT INTO posts VALUES(?,?, ?, ?, ?, ?, ?, ?, ?, ?);",$postContent);
		} else
		{
			// New thread
			$postContent = array(
				$userid,
				$username,
				$title,
				$content,
				$date,
				$id,
				$ip,
				$board,
				$type,
				$latest,
				$imgEntry);
			$query = $this->db->query("INSERT INTO threads VALUES(?,?,?,?,?,?,?,?,?,?,?);",$postContent);
		}

		if($query)
		{
			if($thread==-1)
			{
				$redirdest = base_url().'board/'.$board.'/thread/'.$id;
			} else
			{
				$redirdest = $this->input->post('origin');
			}
			header("Location: " . $redirdest);
		} else
		{
			echo "The post was not successful.";
		}
	}

	// Find all ">greentext" and insert the necessary HTML to make the text green.
	// Run this AFTER the conversion to HTML entities.
	private function greenText($input)
	{
		$contentArray = explode("\n",$input);
		$arLength = count($contentArray);
		for($i=0;$i<$arLength;$i++)
		{
			$line = str_split($contentArray[$i]);
			$lineLength = count($line);
			$greentext = false;
			for($j=0;$j<$lineLength;$j++)
			{
				// The characters &gt; in that order are the HTML entity for the greater than > symbol. Kludgy but it works.
				if(($line[$j]=="&") && ($line[$j+1]=="g") && ($line[$j+2]=="t") && ($line[$j+3]==";"))
				{
					$greentext = true;
				}
			}
			if($greentext)
			{
				$contentArray[$i] = "<span class=".'"'.'greentext'.'"'.">" . $contentArray[$i] . "</span>";
			}
		}
		$output = implode("\n",$contentArray);
		return $output;
	}
	//	Slightly modified version of http://css-tricks.com/snippets/php/find-urls-in-text-make-links/
	private function findURLs($text)
	{
		$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

				// Check if there is a url in the text
		if(preg_match($reg_exUrl, $text, $url)) {

		       		// make the urls hyper links
		       $text = preg_replace($reg_exUrl, "<a href=".'"'.$url[0].'"'.">$url[0]</a> ", $text);
		       return $text;
		} else {

		       		// if no urls in the text just return the text
		       return $text;
		}
	}
	// http://www.jonasjohn.de/snippets/php/post-request.htm
	private function post_request($url, $data, $referer='') {
	 
	    // Convert the data array into URL Parameters like a=b&foo=bar etc.
	    $data = http_build_query($data);
	 
	    // parse the given URL
	    $url = parse_url($url);
	 
	    if ($url['scheme'] != 'http') { 
	        die();
	    }
	 
	    // extract host and path:
	    $host = $url['host'];
	    $path = $url['path'];
	 
	    // open a socket connection on port 80 - timeout: 30 sec
	    $fp = fsockopen($host, 80, $errno, $errstr, 30);
	 
	    if ($fp){
	 
	        // send the request headers:
	        fputs($fp, "POST $path HTTP/1.1\r\n");
	        fputs($fp, "Host: $host\r\n");
	 
	        if ($referer != '')
	            fputs($fp, "Referer: $referer\r\n");
	 
	        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	        fputs($fp, "Content-length: ". strlen($data) ."\r\n");
	        fputs($fp, "Connection: close\r\n\r\n");
	        fputs($fp, $data);
	 
	        $result = ''; 
	        while(!feof($fp)) {
	            // receive the results of the request
	            $result .= fgets($fp, 128);
	        }
	    }
	    else { 
	        return array(
	            'status' => 'err', 
	            'error' => "$errstr ($errno)"
	        );
	    }
	 
	    // close the socket connection:
	    fclose($fp);
	 
	    // split the result header from the content
	    $result = explode("\r\n\r\n", $result, 2);
	 
	    $header = isset($result[0]) ? $result[0] : '';
	    $content = isset($result[1]) ? $result[1] : '';
	 
	    // return as structured array:
	    return array(
	        'status' => 'ok',
	        'header' => $header,
	        'content' => $content
	    );
	}
}