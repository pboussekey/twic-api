INSERT IGNORE INTO `permission` (`libelle`) VALUES ('mail.getTpl');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'mail.getTpl'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('mail.addTpl');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'mail.addTpl'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('eventcomment.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'eventcomment.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('eventcomment.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'eventcomment.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('bankquestiontag.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'bankquestiontag.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('eventcomment.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'eventcomment.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.getListSubmissions');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.getListSubmissions'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.addDocument');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.addDocument'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.deleteDocument');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.deleteDocument'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('component.getListEqCq');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'component.getListEqCq'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('connection.getAvg');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'connection.getAvg'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('eventuser.view');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'eventuser.view'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('message.getListTag');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'message.getListTag'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.join');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.join'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('poll.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'poll.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('poll.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'poll.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('poll.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'poll.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('poll.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'poll.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('poll.vote');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'poll.vote'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('gradingpolicy.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'gradingpolicy.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('gradingpolicy.getListByCourse');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'gradingpolicy.getListByCourse'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('assignment.isGroupWorkSubmitted');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'assignment.isGroupWorkSubmitted'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('document.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'document.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('document.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'document.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('document.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'document.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('grading.getByProgram');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'grading.getByProgram'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('grading.updateProgram');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'grading.updateProgram'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('activities.getListWithUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'activities.getListWithUser'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.getListContact');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.getListContact'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('activity.getListWithUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'activity.getListWithUser'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('mail.getListTpl');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'mail.getListTpl'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.lostPassword');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.lostPassword'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.login');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.login'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconfinvitation.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconfinvitation.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconfinvitation.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconfinvitation.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.join');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.join'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.validTransfertVideo');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.validTransfertVideo'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.record');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.record'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.implode');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.implode'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.getBySubmission');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.getBySubmission'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.getRoom');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.getRoom'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.getListVideoUpload');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.getListVideoUpload'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (0, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('program.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'program.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('program.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'program.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.getListLite');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.getListLite'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.addProgram');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.addProgram'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.removeProgram');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.removeProgram'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.logout');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.logout'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('program.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'program.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('program.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'program.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('program.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'program.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.getIdentity');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.getIdentity'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.deleteProgram');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.deleteProgram'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('course.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'course.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.addCourse');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.addCourse'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.deleteCourse');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.deleteCourse'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('course.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'course.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('course.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'course.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('course.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'course.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('course.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'course.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('module.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'module.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('gradingpolicy.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'gradingpolicy.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('module.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'module.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('module.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'module.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('module.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'module.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('gradingpolicy.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'gradingpolicy.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('materialdocument.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'materialdocument.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('materialdocument.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'materialdocument.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('materialdocument.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'materialdocument.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.getListGrade');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.getListGrade'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('assignment.addComment');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'assignment.addComment'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('assignment.setGrade');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'assignment.setGrade'));
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'assignment.setGrade'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('gradingpolicygrade.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'gradingpolicygrade.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.getListGradeDetail');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.getListGradeDetail'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('gradingpolicygradecomment.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'gradingpolicygradecomment.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.getListByModule');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.getListByModule'));
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.getListByModule'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('assignment.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'assignment.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('grading.getBySchool');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'grading.getBySchool'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('task.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'task.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('task.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'task.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('task.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'task.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('task.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'task.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.getStudentList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.getStudentList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('assignment.getSubmission');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'assignment.getSubmission'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('assignment.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'assignment.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('assignment.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'assignment.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('itemassignmentcomment.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'itemassignmentcomment.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('course.getListRecord');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'course.getListRecord'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('school.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'school.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('school.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'school.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('school.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'school.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('school.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'school.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.accept');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.accept'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.remove');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.remove'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('contact.getListRequest');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'contact.getListRequest'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('research.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'research.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.joinUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.joinUser'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconfdoc.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconfdoc.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconfdoc.getListByVideoconf');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconfdoc.getListByVideoconf'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.addConversation');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.addConversation'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.getConversation');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.getConversation'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversationuser.getConversationByUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversationuser.getConversationByUser'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('message.send');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'message.send'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('message.sendVideoConf');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'message.sendVideoConf'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('permission.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'permission.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('permission.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'permission.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('permission.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'permission.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('permission.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'permission.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('rolepermission.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'rolepermission.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('rolepermission.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'rolepermission.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.startRecord');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.startRecord'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconf.stopRecord');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconf.stopRecord'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('country.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'country.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('message.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'message.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.read');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.read'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('message.read');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'message.read'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('message.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'message.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('message.sendMail');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'message.sendMail'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('message.getListConversation');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'message.getListConversation'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('assignment.removeDocument');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'assignment.removeDocument'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('assignment.addDocument');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'assignment.addDocument'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('conversation.unRead');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'conversation.unRead'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('message.unRead');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'message.unRead'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('feed.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'feed.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('feed.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'feed.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('feed.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'feed.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('feed.addComment');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'feed.addComment'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('feed.deleteComment');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'feed.deleteComment'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('feed.getListComment');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'feed.getListComment'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('like.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'like.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('like.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'like.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('like.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'like.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('feed.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'feed.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('resume.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'resume.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('resume.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'resume.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('resume.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'resume.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('resume.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'resume.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.updatePassword');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.updatePassword'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('feed.linkPreview');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'feed.linkPreview'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('address.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'address.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('address.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'address.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('address.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'address.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('address.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'address.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('city.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'city.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('city.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'city.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('city.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'city.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('city.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'city.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('division.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'division.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('division.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'division.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('division.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'division.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('division.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'division.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('questionnaire.answer');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'questionnaire.answer'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('answer.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'answer.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('questionnaire.getAnswer');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'questionnaire.getAnswer'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('thread.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'thread.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('thread.getNbrMessage');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'thread.getNbrMessage'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('message.getNbrMessage');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'message.getNbrMessage'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('thread.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'thread.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('thread.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'thread.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('thread.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'thread.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('threadmessage.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'threadmessage.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('threadmessage.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'threadmessage.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('threadmessage.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'threadmessage.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('threadmessage.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'threadmessage.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('component.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'component.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('component.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'component.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('component.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'component.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('component.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'component.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('thread.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'thread.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('school.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'school.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('event.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'event.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('userlanguage.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'userlanguage.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('userlanguage.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'userlanguage.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('userlanguage.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'userlanguage.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('userlanguage.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'userlanguage.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('dimension.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'dimension.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('dimension.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'dimension.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('dimension.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'dimension.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('dimension.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'dimension.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('question.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'question.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('question.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'question.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('question.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'question.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('question.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'question.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('component.getListWithScale');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'component.getListWithScale'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('gradingpolicygrade.setGrade');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'gradingpolicygrade.setGrade'));
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'gradingpolicygrade.setGrade'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('language.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'language.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('languagelevel.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'languagelevel.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('eventuser.read');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'eventuser.read'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('activity.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'activity.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('activity.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'activity.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('activity.aggregate');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'activity.aggregate'));
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'activity.aggregate'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('course.getListDetail');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'course.getListDetail'));
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'course.getListDetail'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('scale.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'scale.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('scale.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'scale.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('scale.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'scale.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('scale.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'scale.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('dimensionscale.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'dimensionscale.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('dimensionscale.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'dimensionscale.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('dimensionscale.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'dimensionscale.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('dimensionscale.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'dimensionscale.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('component.getEqCq');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'component.getEqCq'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('componentscale.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'componentscale.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('componentscale.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'componentscale.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('componentscale.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'componentscale.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('componentscale.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'componentscale.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('guidelines.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'guidelines.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('guidelines.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'guidelines.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('guidelines.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'guidelines.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('guidelines.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'guidelines.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('guidelines.isViewed');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'guidelines.isViewed'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.getListGradeItem');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.getListGradeItem'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('materialdocument.getListByItem');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'materialdocument.getListByItem'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('materialdocument.nbrView');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'materialdocument.nbrView'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('group.replaceUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'group.replaceUser'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('group.deleteUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'group.deleteUser'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('group.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'group.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('group.addUser');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'group.addUser'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('group.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'group.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('group.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'group.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('set.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'set.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('set.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'set.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('set.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'set.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('set.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'set.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('set.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'set.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('ctrate.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'ctrate.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('ctrate.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'ctrate.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('ctrate.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'ctrate.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('ctgroup.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'ctgroup.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('ctgroup.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'ctgroup.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('ctgroup.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'ctgroup.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('ctdone.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'ctdone.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('ctdone.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'ctdone.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('ctdone.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'ctdone.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('ctdate.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'ctdate.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('ctdate.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'ctdate.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('ctdate.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'ctdate.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('group.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4,
(SELECT `id` FROM `permission` WHERE `libelle`= 'group.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('library.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'library.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('library.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'library.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('library.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'library.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('library.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'library.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('library.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'library.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('library.getSession');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'library.getSession'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('optgrading.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'optgrading.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('optgrading.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'optgrading.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('optgrading.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'optgrading.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('bankquestion.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'bankquestion.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('bankquestion.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'bankquestion.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('bankquestion.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'bankquestion.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('bankquestion.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'bankquestion.getList'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('poll.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'poll.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.getListUsers');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.getListUsers'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('message.sendAssignment');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'message.sendAssignment'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('threadmessage.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'threadmessage.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('pollitem.replace');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'pollitem.replace'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.getContent');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.getContent'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.submitBySubmission');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.submitBySubmission'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.submit');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.submit'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.submitByItem');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.submitByItem'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.cancelsubmit');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.cancelsubmit'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.cancelsubmitByItem');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.cancelsubmitByItem'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('submission.cancelsubmitBySubmission');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'submission.cancelsubmitBySubmission'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('texteditor.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'texteditor.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('texteditor.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'texteditor.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('texteditor.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'texteditor.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('videoconfopt.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'videoconfopt.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('poll.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'poll.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('item.getListForCalendar');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'item.getListForCalendar'));

