<?php
/**
 * @SWG\parameter(parameter="pAuthorization", type="string", name="Authorization",in="header",required=true,)
 * @SWG\Parameter(parameter="pEmail", type="string", name="email", required=true, in="body",
 *      @SWG\Schema(type="object", example={"email":"1@1.com"}, title="json",
 *          @SWG\Property(type="string", property="email")
 *      )
 * )
 *
 * @SWG\Parameter(
 *     parameter="pFilter",
 *     in="query",
 *     type="string",
 *     name="filter",
 *     description="query string filter",
 * )
 * @SWG\Parameter(
 *     parameter="pId",
 *     in="path",
 *     type="string",
 *     name="id",
 *     description="path - id",
 * )
 * @SWG\Parameter(
 *     parameter="pPage",
 *     in="query",
 *     type="string",
 *     name="page",
 *     description="query string page",
 * )
 * @SWG\Parameter(
 *     parameter="pPer_page",
 *     in="query",
 *     type="string",
 *     name="per_page",
 *     description="query string per_page",
 * )
 * @SWG\Parameter(
 *     parameter="pSort",
 *     in="query",
 *     type="string",
 *     name="sort",
 *     description="query string sort",
 * )
 * @SWG\Parameter(
 *     parameter="pColumns",
 *     in="query",
 *     type="string",
 *     name="columns",
 *     description="query string columns",
 * )
 * @SWG\Parameter(
 *     parameter="pPageName",
 *     in="query",
 *     type="string",
 *     name="pageName",
 *     description="query string page name",
 * )
 */
