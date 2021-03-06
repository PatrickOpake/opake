<div ng-controller="AnalyticsCredentialsCtrl as crListVm" ng-init="crListVm.type = 'medical'; crListVm.search();" ng-cloak>
    <div class="content-block credentials-admin-alert">
        {{ crListVm.staffs_with_approaching_dates_count }} Staff have expiration dates approaching or passed 
    </div>
    <div class="content-block credentials-list">
        <filters-panel ctrl="crListVm">
            <div class="data-row">
                <label>Staff Name</label>
                <opk-select ng-model="crListVm.search_params.user"
                            options="user.id as user.fullname for user in source.getMedicalStaffs()"></opk-select>
            </div>
            <div class="data-row checkbox">
                <input id="with_expired_dates" type="checkbox" class="styled" ng-model="crListVm.search_params.with_expired_dates">
                <label for="with_expired_dates">Display users with expires dates</label>
            </div>
        </filters-panel>
        <div class="list-control">
            <div class="pull-right">
                <a class="btn btn-success" href="" ng-click="crListVm.exportMedicalStaffs()">Download</a>
            </div>
        </div>

        <table class="opake" ng-if="crListVm.items.length">
            <thead sorter="crListVm.search_params" callback="crListVm.search()">
            <tr>
                <th sort="user_name">Physician </th>
                <th>NPI #</th>
				<th>TIN</th>
				<th>Taxonomy Code</th>
                <th class="with-badge">
                    Med License
                    <span class="badge" ng-if="crListVm.expired_count.medical_licence_exp_date">
                        {{ crListVm.expired_count.medical_licence_exp_date }}
                    </span>
                </th>
                <th class="with-badge">
                    DEA #
                    <span class="badge" ng-if="crListVm.expired_count.dea_exp_date">
                        {{ crListVm.expired_count.dea_exp_date }}
                    </span>
                </th>
                <th class="with-badge">
                    CDS
                    <span class="badge" ng-if="crListVm.expired_count.cds_exp_date">
                        {{ crListVm.expired_count.cds_exp_date }}
                    </span>
                </th>
                <th>ECFMG</th>
                <th class="with-badge">
                    Insurance
                    <span class="badge" ng-if="crListVm.expired_count.insurance">
                        {{ crListVm.expired_count.insurance }}
                    </span>
                </th>
                <th class="with-badge">
                    ACLS
                    <span class="badge" ng-if="crListVm.expired_count.acls_date">
                        {{ crListVm.expired_count.acls_date }}
                    </span>
                </th>
                <th class="with-badge">
                    Immunization
                    <span class="badge" ng-if="crListVm.expired_count.immunizations">
                        {{ crListVm.expired_count.immunizations }}
                    </span>
                </th>
                <th class="with-badge">
                    Retest Date
                    <span class="badge" ng-if="crListVm.expired_count.retest_date">
                        {{ crListVm.expired_count.retest_date }}
                    </span>
                </th>
                <th>UPIN</th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="item in crListVm.items">
                <td class="physician centered">
                    <a href="" ng-click="crListVm.redirectToUserCredentialsPage(item.user_id)">
                        {{ item.user.last_name }}, {{ ::item.user.first_name }}
                    </a>
                </td>
                <td>
                    <div class="name">
                        <a ng-if="item.npi_file_url" href="" ng-click="crListVm.downloadFile(item.npi_file_url)" uib-tooltip="Click to Download">
                            {{ item.npi_number }}
                        </a>
                        <span ng-if="!item.npi_file_url">{{ item.npi_number }}</span>
                    </div>
                    <div class="upload-file">
                        <a href=""
                           uib-tooltip="Upload File"
                           class="btn-file"
                           select-file
                           on-select="crListVm.uploadFile(item, files, 'npi_file')">
                            <i ng-show="item.npi_file_url" class="icon-pdf"></i>
                            <i ng-show="!item.npi_file_url" class="icon-upload-pdf"></i>
                            <input type="file" name="fileDoc"/>
                        </a>
                    </div>
                </td>
				<td class="centered">{{item.tin}}</td>
				<td class="centered">{{item.taxonomy_code}}</td>
                <td>
                    <div class="name">
                        <a ng-if="item.medical_licence_file_url" href="" ng-click="crListVm.downloadFile(item.medical_licence_file_url)" uib-tooltip="Click to Download">
                            {{ item.medical_licence_number }}
                        </a>
                        <span ng-if="!item.medical_licence_file_url">{{ item.medical_licence_number }}</span>
                    </div>
                    <div class="upload-file">
                        <a href=""
                           uib-tooltip="Upload File"
                           class="btn-file"
                           select-file
                           on-select="crListVm.uploadFile(item, files, 'medical_licence_file')">
                            <i ng-show="item.medical_licence_file_url" class="icon-pdf"></i>
                            <i ng-show="!item.medical_licence_file_url" class="icon-upload-pdf"></i>
                            <input type="file" name="fileDoc"/>
                        </a>
                    </div>
                    Exp:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.medical_licence_exp_date)}">
                        {{ item.medical_licence_exp_date | date:'M/d/yyyy' }}
                    </span>
                </td>
                <td>
                    <div class="name">
                        <a ng-if="item.dea_file_url" href="" ng-click="crListVm.downloadFile(item.dea_file_url)" uib-tooltip="Click to Download">
                            {{ item.dea_number }}
                        </a>
                        <span ng-if="!item.dea_file_url">{{ item.dea_number }}</span>
                    </div>
                    <div class="upload-file">
                        <a href=""
                           uib-tooltip="Upload File"
                           class="btn-file"
                           select-file
                           on-select="crListVm.uploadFile(item, files, 'dea_file')">
                            <i ng-show="item.dea_file_url" class="icon-pdf"></i>
                            <i ng-show="!item.dea_file_url" class="icon-upload-pdf"></i>
                            <input type="file" name="fileDoc"/>
                        </a>
                    </div>
                    Exp:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.dea_exp_date)}">
                        {{ item.dea_exp_date | date:'M/d/yyyy' }}
                    </span>
                </td>
                <td>
                    <div class="name">
                        <a ng-if="item.cds_file_url" href="" ng-click="crListVm.downloadFile(item.cds_file_url)" uib-tooltip="Click to Download">
                            {{ item.cds_number }}
                        </a>
                        <span ng-if="!item.cds_file_url">{{ item.cds_number }}</span>
                    </div>
                    <div class="upload-file">
                        <a href=""
                           uib-tooltip="Upload File"
                           class="btn-file"
                           select-file
                           on-select="crListVm.uploadFile(item, files, 'cds_file')">
                            <i ng-show="item.cds_file_url" class="icon-pdf"></i>
                            <i ng-show="!item.cds_file_url" class="icon-upload-pdf"></i>
                            <input type="file" name="fileDoc"/>
                        </a>
                    </div>
                    Exp:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.cds_exp_date)}">
                        {{ item.cds_exp_date | date:'M/d/yyyy' }}
                    </span>
                </td>
                <td><div class="name"><span>{{ item.ecfmg }}</span></div></td>
                <td>
                    <div class="name">
                        <a ng-if="item.insurance_file_url" href="" ng-click="crListVm.downloadFile(item.insurance_file_url)" uib-tooltip="Click to Download">
                            {{ item.insurance }}
                        </a>
                        <span ng-if="!item.insurance_file_url">{{ item.insurance }}</span>
                    </div>
                    <div class="upload-file">
                        <a href=""
                           uib-tooltip="Upload File"
                           class="btn-file"
                           select-file
                           on-select="crListVm.uploadFile(item, files, 'insurance_file')">
                            <i ng-show="item.insurance_file_url" class="icon-pdf"></i>
                            <i ng-show="!item.insurance_file_url" class="icon-upload-pdf"></i>
                            <input type="file" name="fileDoc"/>
                        </a>
                    </div>
                    Exp:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.insurance_exp_date)}">
                        {{ item.insurance_exp_date | date:'M/d/yyyy' }}
                    </span> <br/>
                    Re-Appt:
                    <span ng-class="{'text-red': crListVm.isDateExpired(item.insurance_reappointment_date)}">
                        {{ item.insurance_reappointment_date | date:'M/d/yyyy' }}
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
                <td class="centered" ng-class="{'text-red': crListVm.isDateExpired(item.retest_date)}">
                    {{ item.retest_date | date:'M/d/yyyy' }}
                </td>
                <td><div class="name"><span>{{ item.upin }}</span></div></td>
            </tr>
            </tbody>
        </table>
        <pages count="crListVm.total_count" page="crListVm.search_params.p" limit="crListVm.search_params.l"
               callback="crListVm.search()"></pages>
        <h4 ng-if="crListVm.items && !crListVm.items.length">Users not found</h4>
    </div>
</div>