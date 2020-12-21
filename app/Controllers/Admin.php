<?php namespace App\Controllers;

class Admin extends BaseController
{
	public function index()
	{
		$data = array();
		$mediaModel = model('App\Models\MediaModel', false);
		$data['medias'] = $mediaModel->findAll();
		$data['header'] = view('common/Header');
		echo view('common/commoncss');
		echo view('Admin/index', $data);
		echo view('common/commonjs');
	}

	public function add()
	{
		$data = array();
		$data['header'] = view('common/Header');

		echo view('common/commoncss');
		echo view('common/commonjs');
		echo view('Admin/add_new_media', $data);
	}

	public function save_new_media()
	{
		$media_name = $this->request->getVar('media_name');
		$media_description = $this->request->getVar('media_description');
		$media_release_date = $this->request->getVar('media_release_date');

		if(!empty($media_name) && !empty($media_description) && !empty($media_release_date))
		{
			$media_data = array(
				'name' => $media_name,
				'description' => $media_description,
				'release_date' => $media_release_date
			);
			$mediaModel = model('App\Models\MediaModel', false);
			$mediaModel->insert($media_data);
			return redirect()->to('/admin');
		}
		else
		{
			return redirect()->back();
		}
	}
}
