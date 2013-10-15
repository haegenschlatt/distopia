<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {
	public function index()
	{
		// API help
		echo "distopia JSON API";
	}

	public function board($board, $page = 0)
	{
		// Won't be written until pagination in the actual board view is written.

	}

	public function thread($thread)
	{
		header("Content-type: application/json");
		//$this->loadThread($thread);
		$out = array();
		// Queries do not include the user's IP.
		$query = $this->db->query("SELECT
			userid,
			username,
			title,
			content,
			date,
			id,
			ip,
			board,
			type,
			image
			FROM threads WHERE id=?",array($thread));
		if($query->num_rows() == 0)
		{
			$this->errorOut("threadDoesNotExist","The requested thread does not exist.");
		}
		$out["op"] = $query->row_array();

		// posts contains the data for each post
		$posts = array();
		// postMap builds the structure
		$postMap = array();
		$query = $this->db->query("SELECT
			userid,
			username,
			content,
			date,
			id,
			board,
			thread,
			parent,
			latest,
			image
			FROM posts WHERE thread=? ORDER BY date ASC;", array($thread));

		foreach($query->result_array() as $row)
		{
			$posts[$row["id"]] = $row;
			// child => parent
			$postMap[$row["id"]] = $row["parent"];
		}

		$postStructure = $this->mapReplies($postMap,$thread,$posts);
		$out["children"] = $postStructure;
		echo json_encode($out);
	}

	public function post()
	{
		
	}

	public function user($user)
	{

	}

/* ------ PRIVATE FUNCTIONS ------ */

	private function mapReplies($postMap, $node, $posts, $hierarchy = 0)
	{
		$result = array();
		foreach($postMap as $post => $parent)
		{
			if($parent == $node)
			{
				unset($postMap[$post]);
				$result[$post] = array(
					"data" => $posts[$post],
					"hierarchy" => $hierarchy,
					"children" => $this->mapReplies($postMap,$post,$posts,($hierarchy+1))
					);
			}
		}
		return $result;
	}

	private function errorOut($code,$humanReadable)
	{
		echo json_encode(array("error" => $code, "humanReadable" => $humanReadable));
		die();
	}

}