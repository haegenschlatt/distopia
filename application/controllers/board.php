<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Board extends CI_Controller {
	public function _remap($request, $request2)
	{
		$this->load->library('DisFunctions');
		$this->disfunctions->checkBan();
		// dis.url/board/request/request2[0]/request2[1]
		if($request == "index")
		{
			//	If someone requests / directly, or just the URL, since board is the default controller.
			//	Note: the frontpage's header is different from the other boards since it does not need the form for posting, so its header is build right into the "frontpage" view.
			$this->load->view("frontpage");
		} else
		{
			if(count($request2)==0)
			{
				//	$request2 is an array, so we count to see how big it is. If there's nothing in it, then the user is just requesting the board, so we load that.
				$this->loadBoard($request,0);
			} else
			{
				//	Otherwise, there IS something after the board request. The next segment is either "page" or "thread".
				if($request2[0]=="thread")
					$this->loadThread($request2[1],$request);
				else if($request2[0]=="page")
					$this->loadBoard($request,$request2[1]);
			}
		}
	}

	private function loadBoard($board,$page)
	{
		$this->load->library('DisFunctions');
		$this->disfunctions->checkBan();
		// Send the board data to the board.
		$query = $this->db->query("SELECT * FROM boardmeta WHERE name=?",$board);
		if($query->num_rows()==0)
		{
			exit("The requested board does not exist.");
		}
		foreach($query->result_array() as $boardmeta)
		{
			// The header view requires the thread ID for the post form. Because the header view is also used for viewing OP, we use -1. This is also stored in the database to represent that the post is an OP.
			$boardmeta['thread'] = -1;
			$this->load->view("header", $boardmeta);
		}
		$query = $this->db->query("SELECT * FROM posts WHERE board=? AND thread=-1 ORDER BY latest DESC LIMIT 10 OFFSET ?;",array($board,$page*20));
		if($query->num_rows()>0)
		{
			foreach($query->result_array() as $postdata)
			{
				//	The only difference between different types of posts are the places they are used:
				//	OP on the front page, reply previews on the front page, the OP in the thread, and replies in the thread.
				//	Thus, the only thing that changes is the styling (and, for OPs on the front page, logic that shows the number of replies.)
				//	So we just pass the desired CSS class to the view.
				$postdata['class'] = "postpreview";
				//	Load in the post preview.
				$this->load->view("showpost",$postdata);
				//	Load in reply previews. Only first-level (direct reply to OP) comments are shown.
				$query2 = $this->db->query("(SELECT * FROM posts WHERE thread=? ORDER BY date DESC LIMIT 5) ORDER BY date ASC",array($postdata['id']));
				foreach($query2->result_array() as $replypreviewdata)
				{
					$replypreviewdata['class'] = "replypreview";
					$this->load->view("showpost",$replypreviewdata);
				}
			}
		}
		$fdata["page"]=$page;
		$fdata["board"]=$board;
		$fdata["type"]="board";
		$this->load->view("footer",$fdata);
	}

	private function loadThread($thread,$board)
	{
		$this->load->library('DisFunctions');
		$this->disfunctions->checkBan();

		// Check the board
		$query = $this->db->query("SELECT * FROM boardmeta WHERE name=?;",array($board));
		if($query->num_rows()==0)
		{
			exit("The requested board does not exist.");
		}
		foreach($query->result_array() as $boardmeta)
		{
			$boardmeta['thread'] = $thread;
			$this->load->view("header", $boardmeta);
		}

		// Check for the OP
		$query = $this->db->query("SELECT * FROM posts WHERE id=? AND board=?",array($thread,$board));
		if($query->num_rows()>0)
		{
			$row = $query->row_array();
			if($row["thread"]!=-1)
			{
				?>
				<p>That is a comment within <a href="<?php echo base_url()."board/$board/thread/".$row["thread"]; ?>">thread <?php echo $row["thread"]; ?></a>.</p>
				<?php
				exit();
			}
			foreach($query->result_array() as $opdata)
			{
				$opdata['class'] = "originalpost";
				$this->load->view("showpost",$opdata);
			}
		} else
		{
			$query = $this->db->query("SELECT * FROM posts WHERE id=?;",array($this->db->escape($thread)));
			if($query->num_rows()>0)
			{
				$result = $query->row_array();
				echo "<p>Thread #" . $thread . " is on [" . $result["board"] . "]. It may have been moved, or you may have used an incorrect URL.</p>";
				echo "<p><a href='" . base_url() . "board/" . $result["board"] . "/thread/" . $result["id"] . "'>Go to thread &raquo;</a></p>";
				exit();
			} else
			{
				exit("Thread not found.");
			}
		}

		$posts = array();
		$postMap = array();
		$query = $this->db->query("SELECT
			name,
			content,
			date,
			id,
			ip,
			board,
			color,
			thread,
			parent,
			latest,
			image
			FROM posts WHERE thread=? AND board=? ORDER BY date ASC;", array($thread,$board));

		foreach($query->result_array() as $row)
		{
			$posts[$row["id"]] = $row;
			// child => parent
			$postMap[$row["id"]] = $row["parent"];
		}

		$postTree = $this->mapReplies($postMap,$thread);

		$this->loadReplies($postTree,$posts);
		$fdata["type"] = "thread";
		$this->load->view("footer",$fdata);
	}

	private function mapReplies($postMap, $node, $hierarchy = 0)
	{
		$result = array();
		foreach($postMap as $post => $parent)
		{
			if($parent == $node)
			{
				unset($postMap[$post]);
				$result[$post] = array(
					"id" => $post,
					"hierarchy" => $hierarchy,
					"children" => $this->mapReplies($postMap,$post,($hierarchy+1))
					);
			}
		}
		return $result;
	}

	private function loadReplies($postTree,$posts)
	{
		foreach($postTree as $id => $data)
		{
			$replydata = $posts[$id];
			$replydata['class'] = "reply";
			$replydata['hierarchy'] = $data["hierarchy"];
			$this->load->view("showpost",$replydata);
			$this->loadReplies($data["children"],$posts);
		}
	}


}
