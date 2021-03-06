<div ng-controller="AnalyticsCredentialsCtrl as crListVm" ng-init="crListVm.type = 'non-surgical'; crListVm.search();" ng-cloak>
    <div class="content-block credentials-admin-alert">
        {{ crListVm.staffs_with_approaching_dates_count }} Staff have expiration dates approaching or passed
    </div>
    <div class="content-block credentials-list">
        <filters-panel ctrl="crListVm">
            <div class="data-row">
                <label>Staff Name</label>
                <opk-select ng-model="crListVm.search_params.user"
                            options="user.id as user.fullname for user in source.getNonSurgicalStaffs()"></opk-select>
            </div>
            <div class="data-row checkbox">
                <input id="with_expired_dates" type="checkbox" class="styled" ng-model="crListVm.search_params.with_expired_dates">
                <label for="with_expired_dates">Display users with expires dates</label>
            </div>
        </filters-panel>
        <div class="list-control">
            <div class="pull-right">
                <a class="btn btn-success" href="" ng-click="crListVm.exportNonSurgicalStaffs()">Download</a>
            </div>
        </div>

        <table class="opake" ng-if="crListVm.items.length">
            <thead sorter="crListVm.search_params" callback="crListVm.search()">
            <tr>
                <th sort="user_name">Name</th>
                <th class="with-badge">
                    Licence #
                    <span class="badge" ng-if="crListVm.expired_count.licence_expr_date">
                        {{ crListVm.expired_count.licence_expr_date }}
                    </span>
                </th>
                <th class="with-badge">
                    BLS
                    <span class="badge" ng-if="crListVm.expired_count.bls_date">
                        {{ crListVm.expired_count.bls_date }}
                    </span>
                </th>
                <th class="with-badge">
                    ACLS
                    <span class="badge" ng-if="crListVm.expired_count.acls_date">
                        {{ crListVm.expired_count.acls_date }}
                    </span>
                </th>
                <th class="with-badge">
                    CNOR
                    <span class="badge" ng-if="crListVm.expired_count.cnor_date">
                        {{ crListVm.expired_count.cnor_date }}
                    </span>
                </th>
                <th class="with-badge">
                    Malpractice
                    <span class="badge" ng-if="crListVm.expired_count.malpractice_exp_date">
                        {{ crListVm.expired_count.malpractice_exp_date }}
                    </span>
                </th>
                <th class="with-badge">
                    H&P 
                    <span class="badge" ng-if="crListVm.expired_count.hp_exp_date">
                        {{ crListVm.expired_count.hp_exp_date }}
                    </span>
                </th>
                <th class="with-badge">
                    Immunizations
                    <span class="badge" ng-if="crListVm.expired_count.immunizations">
                        {{ crListVm.expired_count.immunizations}}
                    </span>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="item in crListVm.items">
                <td class="physician">
                    <a href="" ng-click="crListVm.redirectToUserCredentialsPage(item.user_id)">
                        {{ item.user.last_name }}, {{ ::item.user.first_name }}
                    </a>
                </td>
                <td>
                    <div class="name">
                        <a ng-if="item.licence_file_url" href="" ng-click="crListVm.downloadFile(item.licence_file_url)" uib-tooltip="Click to Download">
                            {{ item.licence_number }}
                        </a>
                        <span ng-if="!item.licence_file_url">{{ item.licence_number }}</span>
                    </div>
                    <div class="upload-file">
                        <a href=""
                           uib-tooltip="Upload File"
                           class="btn-file"
                           select-file
                           on-select="crListVm.uploadFile(item, files, 'licence_file')">
                            <i ng-show="item.licence_file_url" class="icon-pdf"></i>
                            <i ng-show="!item.licence_file_url" class="icon-upload-pdf"></i>
                            <input type="file" name="fileDoc"/>
                        </a>
                    </div>
                    Exp:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.licence_expr_date)}">
                        {{ item.licence_expr_date | date:'M/d/yyyy' }}
                    </span>
                </td>
                <td>
                    <div class="name"></div>
                    <div class="upload-file">
                        <a href=""
                           uib-tooltip="Upload File"
                           class="btn-file"
                           select-file
                           on-select="crListVm.uploadFile(item, files, 'bls_file')">
                            <i ng-show="item.bls_file_url" class="icon-pdf"></i>
                            <i ng-show="!item.bls_file_url" class="icon-upload-pdf"></i>
                            <input type="file" name="fileDoc"/>
                        </a>
                    </div>
                    Exp:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.bls_date)}">
                        {{ item.bls_date | date:'M/d/yyyy' }}
                    </span>
                </td>
                <td>
                    <div class="name"></div>
                    <div class="upload-file">
                        <a href=""
                           uib-tooltip="Upload File"
                           class="btn-file"
                           select-file
                           on-select="crListVm.uploadFile(item, files, 'acls_file')">
                            <i ng-show="item.acls_file_url" class="icon-pdf"></i>
                            <i ng-show="!item.acls_file_url" class="icon-upload-pdf"></i>
                            <input type="file" name="fileDoc"/>
                        </a>
                    </div>
                    Exp:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.acls_date)}">
                        {{ item.acls_date | date:'M/d/yyyy' }}
                    </span>
                </td>
                <td>
                    <div class="name"></div>
                    <div class="upload-file">
                        <a href=""
                           uib-tooltip="Upload File"
                           class="btn-file"
                           select-file
                           on-select="crListVm.uploadFile(item, files, 'cnor_file')">
                            <i ng-show="item.cnor_file_url" class="icon-pdf"></i>
                            <i ng-show="!item.cnor_file_url" class="icon-upload-pdf"></i>
                            <input type="file" name="fileDoc"/>
                        </a>
                    </div>
                    Exp:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.cnor_date)}">
                        {{ item.cnor_date | date:'M/d/yyyy' }}
                    </span>
                </td>
                <td>
                    <div class="name">
                        <a ng-if="item.malpractice_file_url" href="" ng-click="crListVm.downloadFile(item.malpractice_file_url)" uib-tooltip="Click to Download">
                            {{ item.malpractice }}
                        </a>
                        <span ng-if="!item.malpractice_file_url">{{ item.malpractice }}</span>
                    </div>
                    <div class="upload-file">
                        <a href=""
                           uib-tooltip="Upload File"
                           class="btn-file"
                           select-file
                           on-select="crListVm.uploadFile(item, files, 'malpractice_file')">
                            <i ng-show="item.malpractice_file_url" class="icon-pdf"></i>
                            <i ng-show="!item.malpractice_file_url" class="icon-upload-pdf"></i>
                            <input type="file" name="fileDoc"/>
                        </a>
                    </div>
                    Exp:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.malpractice_exp_date)}">
                        {{ item.malpractice_exp_date | date:'M/d/yyyy' }}
                    </span>
                </td>
                <td>
                    <div class="name"></div>
                    <div class="upload-file">
                        <a href=""
                           uib-tooltip="Upload File"
                           class="btn-file"
                           select-file
                           on-select="crListVm.uploadFile(item, files, 'hp_file')">
                            <i ng-show="item.hp_file_url" class="icon-pdf"></i>
                            <i ng-show="!item.hp_file_url" class="icon-upload-pdf"></i>
                            <input type="file" name="fileDoc"/>
                        </a>
                    </div>
                    Exp:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.hp_exp_date)}">
                        {{ item.hp_exp_date | date:'M/d/yyyy' }}
                    </span>
                </td>
                <td class="immunizations">
                    <div class="upload-file">
                        <a href=""
                           uib-tooltip="Upload File"
                           class="btn-file"
                           select-file
                           on-select="crListVm.uploadFile(item, files, 'immunizations_file')">
                            <i ng-show="item.immunizations_file_url" class="icon-pdf"></i>
                            <i ng-show="!item.immunizations_file_url" class="icon-upload-pdf"></i>
                            <input type="file" name="fileDoc"/>
                        </a>
                    </div>
                    PPD:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.immunizations_ppp_due)}">
                        {{ item.immunizations_ppp_due | date:'M/d/yyyy' }}
                    </span><br/>
                    Hep B:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.immunizations_help_b)}">
                        {{ item.immunizations_help_b | date:'M/d/yyyy' }}
                    </span><br/>
                    Rubella:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.immunizations_rubella)}">
                        {{ item.immunizations_rubella | date:'M/d/yyyy' }}
                    </span><br/>
                    Rubeola:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.immunizations_rubeola)}">
                        {{ item.immunizations_rubeola | date:'M/d/yyyy' }}
                    </span><br/>
                    Varicela:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.immunizations_varicela)}">
                        {{ item.immunizations_varicela | date:'M/d/yyyy' }}
                    </span><br/>
                    Mumps:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.immunizations_mumps)}">
                        {{ item.immunizations_mumps | date:'M/d/yyyy' }}
                    </span><br/>
                    Flu:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.immunizations_flue)}">
                        {{ item.immunizations_flue | date:'M/d/yyyy' }}
                    </span>
                </td>
            </tr>
            </tbody>
        </table>
        <pages count="crListVm.total_count" page="crListVm.search_params.p" limit="crListVm.search_params.l"
               callback="crListVm.search()"></pages>
        <h4 ng-if="crListVm.items && !crListVm.items.length">Users not found</h4>
    </div>
</div>