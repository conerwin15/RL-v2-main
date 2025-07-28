<?php 

	return [

				'user' => 4,
				'dealer' => 3,
				'admin' => 2,
				'superadmin' => 1,
				'team' => 'Piaggio',
				'support_email' => 'support-test@yopmail.com',
				'support_name' => 'Piaggio Support Team',
				'thread-open' => 1,
				'thread-close' => 2,
				'thread-deleted' => 4,
				'NEWS_STORAGE_PATH' => '/news-promotions/files/',
				'SALESTIPS_STORAGE_PATH' => '/sales-tips/files/',
				'LEARNING_PATH_MEDIA' => '/learning-path/',
				'LEARNING_PATH_STORAGE' => '/learning-paths',
				'SCROM_COURSE' => '/scorm-course/',
				'NEW_POST' => 'new_post',
				'REMOVE_POST' => 'remove_post',
				'ADD_COMMENT' => 'add_comment',
				'REMOVE_COMMENT' => 'remove_comment',
				'LIKE_POST' => 'like_post',
				'UNLIKE_POST' => 'unlike_post',
				'DIAMOND_BADGE' => 'diamond_badge',
				'SILVER_BADGE' => 'silver_badge',
				'GOLD_BADGE' => 'gold_badge',
				'BRONZE_BADGE' => 'bronze_badge',
				'ADJUST_POINT' => 'adjust_point',
				'PROFILE_PICTURES' => '/profile-pictures/',
				'QUIZ_QUESTION_STORAGE_PATH' => '/quiz-questions/files/',
				'QUIZ_OPTION_STORAGE_PATH' => '/quiz-options/files/',
				'THREAD_IMAGE_STORAGE_PATH' => '/threads/files/',
				'THREAD_REPLY_IMAGE_STORAGE_PATH' => '/threads/replies/files',
				'LEARNING_PACKAGE' => '/learning_package/files/',
				'CHATBOT_STORAGE' => '/chatbot/files/',
				'BANNER_STORAGE' => '/banner/files/',

				'FEATURED_TRAINEE_QUERY' => 'select users.name as name, users.image as image, sum(user_point_history.points) as totalPoints, r.name as region from user_point_history inner join users on user_point_history.user_id = users.id inner join regions r on r.id = users.region_id where users.region_id = :regionId and DATE_FORMAT(user_point_history.created_at, :dateFormat) between  DATE_FORMAT(user_point_history.created_at, :first_date) and DATE_FORMAT(user_point_history.created_at, :last_date) group by users.name, users.image, user_point_history.user_id, r.name order by totalPoints desc limit 1',
				'FEATURED_TRAINEE_QUERY_SUPERADMIN' =>  'select users.name as name, users.image as image, sum(user_point_history.points) as totalPoints, r.name as region from user_point_history inner join users on user_point_history.user_id = users.id inner join regions r on r.id = users.region_id where DATE_FORMAT(user_point_history.created_at, :dateFormat) between  DATE_FORMAT(user_point_history.created_at, :first_date) and DATE_FORMAT(user_point_history.created_at, :last_date) group by users.name, users.image, user_point_history.user_id, r.name order by totalPoints desc limit 1',

				// image and video supported formats
				'SUPPORTED_IMAGE_FORAMTS' => ['jpg', 'jpeg', 'png', 'gif'],
				'SUPPORTED_VIDEO_FORAMTS' => ['mov', 'm4v', 'mp4'],
				'SUPPORTED_DOCUMENT_FORMATS' => ['pdf'],
				'SUPPORTED_MEDIA_FROMATS' => ['jpg', 'jpeg', 'png', 'mov', 'm4v', 'mp4', 'pdf', 'gif'],
				'QUESTION_MEDIA_FROMATS' => ['jpg', 'jpeg', 'png', 'mov', 'm4v', 'mp4', 'gif'],

				// learning path image format
				'LEARNINGPATH_IMAGE_FORAMTS' => ['jpg', 'jpeg', 'png'],

				// learning path scrom format
				'LEARNINGPATH_SCROM_FORAMTS' => ['zip', 'rar'],

				// learning path media format
				'LEARNINGPATH_MEDIA_FORAMTS' => ['jpg', 'jpeg', 'png', 'pdf'],

				'new_post_comment' => 'New Post',
				'remove_post_comment' => 'Remove Post',
				'add_comment' => 'Add Comment',
				'remove_comment_comment' => 'Remove Comment',
				'like_post_comment' => 'Like Post',
				'unlike_post_comment' => 'Unlike Post',
				'diamond_badge_comment' => 'Diamond Badge',
				'silver_badge_comment' => 'Silver Badge',
				'gold_badge_comment' => 'Gold Badge',
				'bronze_badge_comment' => 'Bronze Badge',
				'adjust_point_comment' => 'Bonus Point',
				'QUIZ_SCORE' => 'quiz_score',
				'quiz_score_point' => 'Quiz Score Point',
			


			];

?>