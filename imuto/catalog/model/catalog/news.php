<?php
class ModelCatalogNews extends Model {
	public function getSingleNews($news_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "news i LEFT JOIN " . DB_PREFIX . "news_description id ON (i.news_id = id.news_id) LEFT JOIN " . DB_PREFIX . "news_to_store i2s ON (i.news_id = i2s.news_id) WHERE i.news_id = '" . (int)$news_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");

		return $query->row;
	}

	public function getNewsList($data=array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "news i LEFT JOIN " . DB_PREFIX . "news_description id ON (i.news_id = id.news_id) LEFT JOIN " . DB_PREFIX . "news_to_store i2s ON (i.news_id = i2s.news_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1' AND i.date_available <= NOW()";
		$sql .= " ORDER BY i.sort_order, LCASE(id.title) ASC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 9;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalNews($data=array()) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "news WHERE date_available <= NOW() AND status = '1'");

		return $query->row['total'];
	}
}