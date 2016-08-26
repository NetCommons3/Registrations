/**
 * Created by りか on 2015/02/18.
 */
NetCommonsApp.constant('moment', moment);
//NetCommonsApp.requires.push('ngSanitize');

NetCommonsApp.controller('Registrations.edit.question',
    ['$scope', 'NetCommonsWysiwyg', '$timeout', 'moment', 'registrationsMessages',
      function($scope, NetCommonsWysiwyg, $timeout, moment, registrationsMessages) {

        /**
         * tinymce
         *
         * @type {object}
         */
        $scope.tinymce = NetCommonsWysiwyg.new();

        $scope.isTrue = '1';

        /**
           * variables
           *
           * @type {Object.<string>}
           */
        var variables = {
          /**
               * Relative path to login form
               *
               * @const
               */
          EXPRESSION_NOT_SHOW: '0',
          USES_USE: '1',

          OTHER_CHOICE_TYPE_NO_OTHER_FILED: '0',

          TYPE_OPTION_NUMERIC: '1',
          TYPE_OPTION_DATE: '2',
          TYPE_OPTION_TIME: '3',
          TYPE_OPTION_EMAIL: '4',
          TYPE_OPTION_URL: '5',
          TYPE_OPTION_PHONE_NUMBER: '6',
          TYPE_OPTION_DATE_TIME: '7',

          RESULT_DISPLAY_TYPE_BAR_CHART: '0',

          TYPE_SELECTION: '1',
          TYPE_MULTIPLE_SELECTION: '2',
          TYPE_TEXT: '3',
          TYPE_TEXT_AREA: '4',
          TYPE_MATRIX_SELECTION_LIST: '5',
          TYPE_MATRIX_MULTIPLE: '6',
          TYPE_DATE_AND_TIME: '7',
          TYPE_SINGLE_SELECT_BOX: '8',
          TYPE_EMAIL: '9',
          TYPE_FILE: '10',

          MATRIX_TYPE_ROW_OR_NO_MATRIX: '0',

          SKIP_GO_TO_END: '99999',

          MAX_QUESTION_COUNT: 50,
          MAX_CHOICE_COUNT: 50

        };

        $scope.colorPickerPalette =
            ['#f38631', '#e0e4cd', '#69d2e7', '#68e2a7', '#f64649',
             '#4d5361', '#47bfbd', '#7c4f6c', '#23313c', '#9c9b7f',
             '#be5945', '#cccccc'];

        /**
           * Initialize
           *
           * @return {void}
           */
        $scope.initialize =
            function(frameId, isPublished, registration, prefectures) {

          $scope.frameId = frameId;
          $scope.isPublished = isPublished;
          $scope.registration = registration;
          $scope.registration.registrationPage =
              $scope.toArray(registration.registrationPage);
          $scope.activeTabIndex = 0;
          $scope.prefectures = prefectures;

          // 各ページ処理
          for (var pIdx = 0; pIdx < $scope.registration.registrationPage.length; pIdx++) {

            var page = $scope.registration.registrationPage[pIdx];

            // 項目アコーディオンクローズ
            //$scope.registration.registrationPage[pIdx].isOpen = false;

            // このページの中にエラーがあるか
            $scope.registration.registrationPage[pIdx].hasError = false;
            if (page.errorMessages) {
              $scope.registration.registrationPage[pIdx].hasError = true;
            }

            if (!page.registrationQuestion) {
              continue;
            }

            // 各項目処理
            for (var qIdx = 0; qIdx < page.registrationQuestion.length; qIdx++) {

              var question = $scope.registration.registrationPage[pIdx].
                  registrationQuestion[qIdx];

              // テキスト、１行テキスト、日付け型は集計結果を出さない設定
              if (question.questionType == variables.TYPE_TEXT ||
                  question.questionType == variables.TYPE_TEXT_AREA ||
                  question.questionType == variables.TYPE_EMAIL ||
                  question.questionType == variables.TYPE_FILE ||
                  question.questionType == variables.TYPE_DATE_AND_TIME) {
                $scope.registration.registrationPage[pIdx].
                    registrationQuestion[qIdx].isResultDisplay =
                    variables.EXPRESSION_NOT_SHOW;
              }
              // この項目の中にエラーがあるか
              if (question.errorMessages) {
                $scope.registration.registrationPage[pIdx].
                    registrationQuestion[qIdx].hasError = true;
                $scope.registration.registrationPage[pIdx].hasError = true;
              }

              // 選択肢がないのならここでcontinue;
              if (!question.registrationChoice) {
                continue;
              }
              // 各項目の選択肢があればその選択肢の中に「その他」が入っているかの確認とフラグ設定
              // また項目の選択肢の中にエラーがあるかのフラグ設定
              for (var cIdx = 0; cIdx < question.registrationChoice.length; cIdx++) {

                var choice = question.registrationChoice[cIdx];
                if (choice.otherChoiceType != variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
                  $scope.registration.registrationPage[pIdx].
                      registrationQuestion[qIdx].hasAnotherChoice = true;
                }
                if (choice.errorMessages) {
                  $scope.registration.registrationPage[pIdx].
                      registrationQuestion[qIdx].hasError = true;
                  $scope.registration.registrationPage[pIdx].hasError = true;
                }
              }

            }
          }
        };
        /**
         * toArray
         *
         * 配列型のはずの変数がなぜかObject扱いになる場合があるので念のための変換
         * @return {Array}
         */
        $scope.toArray = function(src) {
          var dst = new Array();
          angular.forEach(src, function(obj, key) {
            obj = $scope._toArray(obj);
            dst[key] = obj;
          });
          return dst;
        };
        /**
         * _toArray
         *
         * toArrayの再帰関数
         * @return {Object}
         */
        $scope._toArray = function(src) {
          var dst = new Object();
          angular.forEach(src, function(obj, key) {
            if (key == 'registrationQuestion' || key == 'registrationChoice') {
              obj = $scope.toArray(obj);
            }
            dst[key] = obj;
          });
          return dst;
        };
        /**
         * アコーディオンヘッダの中のドロップダウンメニューボタンのクリックで
         * アコーディオンが開閉するのを抑止するための
         *
         * @return {String}
         */
        $scope.deter = function($event) {
          $event.preventDefault();
          $event.stopPropagation();
        };
        /**
         * get Date String
         *
         * @return {String}
         */
        $scope.getDateStr = function(dateStr, format) {
          if (! dateStr) {
            return '';
          }
          // もしも時刻表示の場合は本日の日付文字列を付加して日時文字列扱いにする
          var regTime = /^\d{2}:\d{2}:\d{2}$/;
          var regTime2 = /^\d{2}:\d{2}$/;
          if (dateStr.match(regTime) || dateStr.match(regTime2)) {
            var today = new Date();
            dateStr = today.getFullYear() +
                '-' + (today.getMonth() + 1) +
                '-' + today.getDate() +
                ' ' + dateStr;
          }
          // もしも年月日表示の場合は00：00を付加して日時文字列扱いにする
          var regTime3 = /^\d{2}-\d{2}-\d{2}$/;
          if (dateStr.match(regTime3)) {
            dateStr += '00:00';
          }

          if (format) {
            var d = moment(dateStr);
            dateStr = d.format(format);
          }

          return dateStr;
        };

        /**
         * change DateTimePickerType
         *
         * @return {void}
         */
        $scope.changeDatetimepickerType = function(pIdx, qIdx) {
          var page = $scope.registration.registrationPage[pIdx];
          var question = page.registrationQuestion[qIdx];
          var type = question.questionTypeOption;
          var format;
          if (type == variables.TYPE_OPTION_DATE) {
            format = 'YYYY-MM-DD';
          } else if (type == variables.TYPE_OPTION_TIME) {
            format = 'HH:mm';
          } else if (type == variables.TYPE_OPTION_DATE_TIME) {
            format = 'YYYY-MM-DD HH:mm';
          }
          var min = question.min;
          var max = question.max;
          $scope.registration.registrationPage[pIdx].
              registrationQuestion[qIdx].min = $scope.getDateStr(min, format);
          $scope.registration.registrationPage[pIdx].
              registrationQuestion[qIdx].max = $scope.getDateStr(max, format);
        };

        /**
         * focus DateTimePicker
         *
         * @return {void}
         */
        $scope.setMinMaxDate = function(ev, pIdx, qIdx) {
          // 自分のタイプがMinかMaxかを知る
          var curEl = ev.currentTarget;
          var elId = curEl.id;

          var typeMinMax;
          typeMinMax = elId.substr(elId.lastIndexOf('.') + 1);
          var targetEl;
          var targetElId;

          // 相方のデータを取り出す
          if (typeMinMax == 'min') {
            targetElId = elId.substring(0, elId.lastIndexOf('.')) + '.max';
          } else {
            targetElId = elId.substring(0, elId.lastIndexOf('.')) + '.min';
          }
          var targetEl = document.getElementById(targetElId);
          var limitDate = $(targetEl).val();

          // 自分のMinまたはMaxを設定する
          var el = document.getElementById(elId);
          if (limitDate != '') {
            if (typeMinMax == 'min') {
              $(el).data('DateTimePicker').maxDate(limitDate);
            } else {
              $(el).data('DateTimePicker').minDate(limitDate);
            }
          }
        };
        /**
           * Add Registration Page
           *
           * @return {void}
           */
        $scope.addPage = function($event) {
          var page = new Object();
          page['pageTitle'] =
              registrationsMessages.newPageLabel +
              ($scope.registration.registrationPage.length + 1);
          page['pageSequence'] =
              $scope.registration.registrationPage.length;
          page['routeNumber'] = 0;
          page['key'] = '';
          page['registrationQuestion'] = new Array();
          $scope.registration.registrationPage.push(page);

          $scope.addQuestion($event,
              $scope.registration.registrationPage.length - 1);

          if ($event) {
            $event.stopPropagation();
          }
          /*$scope.activeTabIndex =
                $scope.registration.registrationPage.length - 1;
            console.log($scope.activeTabIndex);*/
        };

        /**
           * Delete Registration Page
           *
           * @return {void}
           */
        $scope.deletePage = function(idx, message) {
          if ($scope.registration.registrationPage.length < 2) {
            // 残り１ページは削除させない
            return;
          }
          if (confirm(message)) {
            $scope.registration.registrationPage.splice(idx, 1);
            $scope._resetRegistrationPageSequence();
            // 削除された場合は１枚目のタブを選択するようにする
            $scope.registration.registrationPage[0].tabActive = true;
          }
        };

        /**
           * Registration Page Sequence reset
           *
           * @return {void}
           */
        $scope._resetRegistrationPageSequence = function() {
          for (var i = 0; i < $scope.registration.registrationPage.length; i++) {
            $scope.registration.registrationPage[i].pageSequence = i;
          }
        };

        /**
           * Add Registration Question
           *
           * @return {void}
           */
        $scope.addQuestion = function($event, pageIndex) {
          if ($scope.checkMaxQuestion() == false) {
            alert(registrationsMessages.maxQuestionWarningMsg);
            return;
          }

          var question = new Object();
          if (!$scope.registration.registrationPage[pageIndex].registrationQuestion) {
            $scope.registration.registrationPage[pageIndex].
                registrationQuestion = new Array();
          }
          var newIndex =
              $scope.registration.registrationPage[pageIndex].
                  registrationQuestion.length;
          question['questionValue'] = registrationsMessages.newQuestionLabel + (newIndex + 1);
          question['questionSequence'] = newIndex;
          question['questionType'] = variables.TYPE_SELECTION;
          question['key'] = '';
          question['isRequire'] = 0;
          question['isSkip'] = 0;
          question['isChoiceRandom'] = 0;
          question['isRange'] = 0;
          question['isResultDisplay'] = 1;
          question['resultDisplayType'] =
              variables.RESULT_DISPLAY_TYPE_BAR_CHART;
          question['registrationChoice'] = new Array();
          question['isOpen'] = true;
          $scope.registration.registrationPage[pageIndex].
              registrationQuestion.push(question);

          $scope.addChoice($event,
              pageIndex,
              $scope.registration.registrationPage[pageIndex].
                  registrationQuestion.length - 1,
              0,
              variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED,
              variables.MATRIX_TYPE_ROW_OR_NO_MATRIX);

          if ($event) {
            $event.stopPropagation();
          }
        };

        /**
           * Move Registration Question
           *
           * @return {void}
           */
        $scope.moveQuestion =
            function($event, pageIndex, beforeIdxStr, afterIdxStr) {
          var beforeIdx = parseInt(beforeIdxStr);
          var afterIdx = parseInt(afterIdxStr);
          var beforeQ =
              $scope.registration.registrationPage[pageIndex].
                  registrationQuestion[beforeIdx];
          if (beforeIdx < afterIdx) {
            for (var i = beforeIdx + 1; i <= afterIdx; i++) {
              var tmpQ =
                  $scope.registration.registrationPage[pageIndex].
                      registrationQuestion[i];
              $scope.registration.registrationPage[pageIndex].
                  registrationQuestion.splice(i - 1, 1, tmpQ);
            }
            $scope.registration.registrationPage[pageIndex].
                registrationQuestion.splice(afterIdx, 1, beforeQ);
          }
          else {
            for (var i = beforeIdx; i >= afterIdx; i--) {
              var tmpQ =
                  $scope.registration.registrationPage[pageIndex].
                      registrationQuestion[i - 1];
              $scope.registration.registrationPage[pageIndex].
                  registrationQuestion.splice(i, 1, tmpQ);
            }
            $scope.registration.registrationPage[pageIndex].
                registrationQuestion.splice(afterIdx, 1, beforeQ);
          }
          $scope._resetRegistrationQuestionSequence(pageIndex);
          $event.preventDefault();
          $event.stopPropagation();
        };

        /**
           * Move to another page Registration Question
           *
           * @return {void}
           */
        $scope.copyQuestionToAnotherPage =
            function($event, pageIndex, qIndex, copyPageIndex) {
          if ($scope.checkMaxQuestion() == false) {
            alert(registrationsMessages.maxQuestionWarningMsg);
            return;
          }

          var tmpQ = angular.copy(
              $scope.registration.registrationPage[pageIndex].registrationQuestion[qIndex]);
          $scope.registration.registrationPage[copyPageIndex].registrationQuestion.push(tmpQ);

          $scope._resetRegistrationQuestionSequence(copyPageIndex);
          //$event.stopPropagation();
        };

        /**
         * Delete Registration Question
         *
         * @return {void}
         */
        $scope.deleteQuestion = function($event, pageIndex, idx, message) {
          if ($scope.registration.registrationPage[pageIndex].
              registrationQuestion.length < 2) {
            return;
          }
          if (confirm(message)) {
            $scope.registration.registrationPage[pageIndex].
                registrationQuestion.splice(idx, 1);
            $scope._resetRegistrationQuestionSequence(pageIndex);
          }
          // ここでやってはいけない！ページの再読み込みが走る
          //$event.stopPropagation();
        };

        /**
           * Registration Question Sequence reset
           *
           * @return {void}
           */
        $scope._resetRegistrationQuestionSequence = function(pageIndex) {
          for (var i = 0; i < $scope.registration.registrationPage[pageIndex].
              registrationQuestion.length; i++) {
            $scope.registration.registrationPage[pageIndex].
                registrationQuestion[i].questionSequence = i;
          }
        };

        /**
           * Add Registration Choice
           *
           * @return {void}
           */
        $scope.addChoice =
            function($event, pIdx, qIdx, choiceCount, otherType, matrixType, choiceLabel) {
          if (choiceCount == variables.MAX_CHOICE_COUNT) {
            alert(registrationsMessages.maxChoiceWarningMsg);
            return;
          }

          if (! choiceLabel) {
            choiceLabel = '';
          }
          var page = $scope.registration.registrationPage[pIdx];
          var question = page.registrationQuestion[qIdx];
          var choice = new Object();
          var choiceColorIdx = choiceCount % $scope.colorPickerPalette.length;

          if (!question.registrationChoice) {
            $scope.registration.registrationPage[pIdx].
                registrationQuestion[qIdx].registrationChoice = new Array();
          }
          var newIndex = question.registrationChoice.length;

          if (choiceLabel) {
            choice['choiceLabel'] = choiceLabel;
          } else if (otherType != variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
            choice['choiceLabel'] = registrationsMessages.newChoiceOtherLabel;
          } else {
            if (matrixType == variables.MATRIX_TYPE_ROW_OR_NO_MATRIX) {
              choice['choiceLabel'] = registrationsMessages.newChoiceLabel + (choiceCount + 1);
            } else {
              choice['choiceLabel'] =
                  registrationsMessages.newChoiceColumnLabel + (choiceCount + 1);
            }
          }
          // skipPageIndex仮設定
          if (pIdx == $scope.registration.registrationPage.length - 1) {
            choice['skipPageSequence'] = variables.SKIP_GO_TO_END;
          } else {
            choice['skipPageSequence'] = parseInt(page['pageSequence']) + 1;
          }

          // その他選択肢は必ず最後にするためにいったん取りのけておく
          var otherChoice = null;
          for (var i = 0; i < question.registrationChoice.length; i++) {
            if (question.registrationChoice[i].otherChoiceType !=
                variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
              otherChoice = question.registrationChoice[i];
              $scope.registration.registrationPage[pIdx].
                  registrationQuestion[qIdx].registrationChoice.splice(i, 1);
            }
          }

          if (otherChoice) {
            choice['choiceSequence'] = newIndex - 1;
            otherChoice['choiceSequence'] = newIndex;
          } else {
            choice['choiceSequence'] = newIndex;
          }

          choice['otherChoiceType'] = otherType;
          choice['matrixType'] = matrixType;
          choice['key'] = '';
          if (otherType != variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
            choiceColorIdx = choice['choiceSequence'] % $scope.colorPickerPalette.length;
          }
          choice['graphColor'] = $scope.colorPickerPalette[choiceColorIdx];

          // 指定された新しい選択肢を追加する
          $scope.registration.registrationPage[pIdx].
              registrationQuestion[qIdx].registrationChoice.push(choice);
          // 取りのけておいたその他選択肢を元通り最後に追加する
          if (otherChoice != null) {
            $scope.registration.registrationPage[pIdx].
                registrationQuestion[qIdx].registrationChoice.push(otherChoice);
          }

          if ($event != null) {
            $event.stopPropagation();
          }
        };

        $scope.addPrefecture =
            function($event, pIdx, qIdx, choiceCount, otherType, matrixType) {
          // `都道府県メタデータの挿入
          angular.forEach($scope.prefectures, function(prefecture, index) {
            $scope.addChoice($event, pIdx, qIdx, choiceCount, otherType, matrixType, prefecture);
          });
        };
        /**
         * Change Another Choice
         *
         * @return {void}
         */
        $scope.changeAnotherChoice = function(pIdx, qIdx, otherType, matrixType) {

          var question = $scope.registration.
              registrationPage[pIdx].registrationQuestion[qIdx];

          //その他を持つように指示されている
          if (question.hasAnotherChoice) {
            $scope.addChoice(null, pIdx, qIdx, 0, otherType, matrixType);
          } else {
            // その他選択肢をなくすように指示されている
            for (var i = 0; i < question.registrationChoice.length; i++) {
              if (question.registrationChoice[i].otherChoiceType !=
                  variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
                $scope.registration.registrationPage[pIdx].
                    registrationQuestion[qIdx].registrationChoice.splice(i, 1);
              }
            }
          }
          $scope._resetRegistrationChoiceSequence(pIdx, qIdx);
        };
        /**
           * Change skip page about Choice
           *
           * @return {void}
           */
        $scope.changeSkipPageChoice = function(skipPageIndex) {
          skipPageIndex = parseInt(skipPageIndex);
          // 選択中のoptionを調べる
          if (skipPageIndex == variables.SKIP_GO_TO_END) {
            return;
          }
          if ($scope.registration.registrationPage.length - 1 >= skipPageIndex) {
            return;
          }
          // ないページを指定された場合は新しく作る
          $scope.addPage(null);
        };
        /**
           * Delete Registration Choice
           *
           * @return {void}
           */
        $scope.deleteChoice = function($event, pIdx, qIdx, seq) {

          var question = $scope.registration.
              registrationPage[pIdx].registrationQuestion[qIdx];

          if (question.registrationChoice.length < 2) {
            return;
          }
          for (var i = 0;
              i < question.registrationChoice.length; i++) {
            if (question.registrationChoice[i].choiceSequence == seq) {
              $scope.registration.registrationPage[pIdx].
                  registrationQuestion[qIdx].registrationChoice.splice(i, 1);
            }
          }
          $scope._resetRegistrationChoiceSequence(pIdx, qIdx);

          if ($event) {
            $event.stopPropagation();
          }
        };
        /**
           * Registration Choice Sequence reset
           *
           * @return {void}
           */
        $scope._resetRegistrationChoiceSequence = function(pageIndex, qIndex) {
          var len = $scope.registration.registrationPage[pageIndex].
              registrationQuestion[qIndex].registrationChoice.length;
          for (var i = 0; i < len; i++) {
            $scope.registration.registrationPage[pageIndex].
                registrationQuestion[qIndex].registrationChoice[i].
                choiceSequence = i;
          }
        };
        /**
           * change Question Type
           *
           * @return {void}
           */
        $scope.changeQuestionType = function($event, pIdx, qIdx) {
          var questionType = $scope.registration.registrationPage[pIdx].
              registrationQuestion[qIdx].questionType;
          // スキップロジックが使えない種類の項目になっていたら
          // スキップ設定をなくす
          if (questionType != variables.TYPE_SELECTION &&
              questionType != variables.TYPE_SINGLE_SELECT_BOX) {
            $scope.registration.registrationPage[pIdx].
                registrationQuestion[qIdx].isSkip = 0;
            $scope.registration.registrationPage[pIdx].
                registrationQuestion[qIdx].isChoiceRandom = 0;
          }
          // 集計結果表示ができない種類の項目になっていたら
          // 集計表示設定をなくす
          if ($scope.isDisabledDisplayResult(questionType)) {
            $scope.registration.registrationPage[pIdx].
                registrationQuestion[qIdx].isResultDisplay = 0;
          } else {
            // それ以外の時はとりあえず集計表示をONにしておく
            $scope.registration.registrationPage[pIdx].
                registrationQuestion[qIdx].isResultDisplay = 1;
          }
          // 日付タイプにされていたらオプション設定は「日付」にしておく
          if (questionType == variables.TYPE_DATE_AND_TIME) {
            $scope.registration.registrationPage[pIdx].
                registrationQuestion[qIdx].
                    questionTypeOption = variables.TYPE_OPTION_DATE;
          }
          // テキストなどのタイプから選択肢などに変更されたとき
          // 選択肢要素が一つもなくなっている場合があるので最低一つは存在するように
          if (!$scope.registration.registrationPage[pIdx].
              registrationQuestion[qIdx].registrationChoice ||
              $scope.registration.registrationPage[pIdx].
              registrationQuestion[qIdx].registrationChoice.length == 0) {
            $scope.addChoice($event,
                pIdx,
                $scope.registration.registrationPage[pIdx].
                    registrationQuestion.length - 1,
                0,
                variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED,
                variables.MATRIX_TYPE_ROW_OR_NO_MATRIX);
          }
        };
        /**
         * Registration Judgment sentence greater than
         *
         * @return {bool}
         */
        $scope.greaterThan = function(prop, tgt2) {
          return function(item) {
            return item[prop] > tgt2[prop];
          }
        };
        /**
         * Registration is able set jump page and skip page
         *
         * @return {bool}
         */
        $scope.isDisabledSetSkip = function(page, question) {
          // ページの中の項目をチェック
          for (var i = 0; i < page.registrationQuestion.length; i++) {
            // もしも項目が引数で指定されているものである場合はチェックしない（continue)
            if (question && page.registrationQuestion[i] == question) {
              continue;
            }
            // スキップが設定されている？
            if (page.registrationQuestion[i].isSkip == variables.USES_USE) {
              // スキップが設定されている場合はtrueを返す
              // return true is disabled
              return true;
            }
          }
          return false;
        };
        /**
         * Registration type is able display result ?
         *
         * @return {bool}
         */
        $scope.isDisabledDisplayResult = function(questionType) {
          if (questionType == variables.TYPE_TEXT ||
              questionType == variables.TYPE_TEXT_AREA ||
              questionType == variables.TYPE_EMAIL ||
              questionType == variables.TYPE_FILE ||
              questionType == variables.TYPE_DATE_AND_TIME) {
            return true;
          }
          return false;
        };
        /**
         * 結果画面でアコーディオンの色を決定する
         *
         * @return {bool}
         */
        $scope.getResultAccordionClass = function(question) {
          if (question.isResultDisplay != variables.EXPRESSION_NOT_SHOW) {
            return 'panel-success';
          } else {
            return 'panel-default';
          }
        };
        /**
         * 現在の質問数に＋１したらMAXを超えてしまうかどうかのガード
         *
         * @return {bool}
         */
        $scope.checkMaxQuestion = function() {
          var ct = 0;
          var pageArr = $scope.registration.registrationPage;
          for (var i = 0; i < pageArr.length; i++) {
            ct += pageArr[i].registrationQuestion.length;
          }
          if (ct + 1 > variables.MAX_QUESTION_COUNT) {
            return false;
          }
          return true;
        };

      }]);
